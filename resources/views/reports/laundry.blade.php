@extends('layouts.app')

@section('title', 'Laundry Report')
@section('page_title', 'Laundry Department Report')

@push('styles')
<style>
    @media print {
        .d-print-none {
            display: none !important;
        }
        .report-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .report-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center d-print-none">
        <span>Report Controls</span>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>
    <div class="card-body bg-light border-bottom d-print-none">
        <form method="GET" class="row gx-2 gy-2 align-items-center">
            <div class="col-auto">
                <label for="from" class="visually-hidden">From</label>
                <input type="date" class="form-control form-control-sm" id="from" name="from" value="{{ $from->format('Y-m-d') }}">
            </div>
            <div class="col-auto">
                <label for="to" class="visually-hidden">To</label>
                <input type="date" class="form-control form-control-sm" id="to" name="to" value="{{ $to->format('Y-m-d') }}">
            </div>
            <div class="col-auto">
                <label for="organization_id" class="visually-hidden">Organization</label>
                <select name="organization_id" id="organization_id" class="form-select form-select-sm">
                    <option value="">All Organizations</option>
                    @foreach($organizations as $org)
                        <option value="{{ $org->id }}" {{ $organization_id == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">Generate Report</button>
            </div>
        </form>
    </div>

    <div class="card-body" id="print-area">
        <div class="report-header text-center mb-4 pb-3 border-bottom">
            <h2 class="fw-bold mb-1">{{ config('app.name', 'Gold Plus Hotel') }}</h2>
            <h4 class="text-uppercase mb-2">Laundry Department Report</h4>
            @if($selected_organization)
                <h5 class="text-primary mb-2">{{ $selected_organization->name }}</h5>
            @endif
            <p class="mb-0 text-muted">
                <strong>Period:</strong> {{ $from->format('M d, Y') }} - {{ $to->format('M d, Y') }}
            </p>
            <p class="mb-0 text-muted small">Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="border rounded p-3 bg-light text-center">
                    <h6 class="text-uppercase text-muted mb-1">Total Orders</h6>
                    <h3 class="mb-0">{{ $orders->count() }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 bg-light text-center">
                    <h6 class="text-uppercase text-muted mb-1">Completed / Delivered</h6>
                    <h3 class="mb-0 text-success">{{ $orders->whereIn('status', ['done', 'delivered'])->count() }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border rounded p-3 bg-light text-center">
                    <h6 class="text-uppercase text-muted mb-1">Total Revenue</h6>
                    <h3 class="mb-0">GH₵{{ number_format($orders->where('status', '!=', 'cancelled')->sum('total_amount'), 2) }}</h3>
                </div>
            </div>
        </div>

        <h5 class="mb-3">Order Details</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Order No.</th>
                        <th>Guest / Room</th>
                        <th>Status</th>
                        <th class="text-end">Amount (GH₵)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sum = 0; @endphp
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $order->order_number }}</td>
                            <td>
                                {{ $order->customer->name ?? 'N/A' }} 
                                @if($order->booking)
                                    <br><small class="text-muted">Room: {{ $order->booking->room->room_number }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($order->status) {
                                        'delivered', 'done' => 'success',
                                        'cancelled' => 'danger',
                                        'processing' => 'info',
                                        default => 'warning'
                                    };
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if($order->status != 'cancelled')
                                    @php $sum += $order->total_amount; @endphp
                                    {{ number_format($order->total_amount, 2) }}
                                @else
                                    <del class="text-muted">{{ number_format($order->total_amount, 2) }}</del>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No laundry orders found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($orders->count() > 0)
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="4" class="text-end">Total Revenue</td>
                        <td class="text-end border-top-2 border-dark">GH₵{{ number_format($sum, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        
        <div class="mt-5 text-end d-print-inline-block w-100" style="page-break-inside: avoid;">
            <p class="mb-5">Authorized Signature:</p>
            <p>___________________________</p>
        </div>
    </div>
</div>
@endsection
