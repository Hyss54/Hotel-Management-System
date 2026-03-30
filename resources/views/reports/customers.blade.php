@extends('layouts.app')

@section('title', 'Guest Report')
@section('page_title', 'Guest Report')

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
        <span>Guest List Report</span>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>
    <div class="card-body bg-light border-bottom d-print-none">
        <form method="GET" class="row gx-2 gy-2 align-items-center">
            <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search name, phone..." value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">Search</button>
                <a href="{{ route('reports.customers') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>

    <div class="card-body" id="print-area">
        <div class="report-header text-center mb-4 pb-3 border-bottom">
            <h2 class="fw-bold mb-1">{{ config('app.name', 'Gold Plus Hotel') }}</h2>
            <h4 class="text-uppercase mb-2">Guest / Customer Report</h4>
            <p class="mb-0 text-muted small">Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>Contact info</th>
                        <th>Type / Organization</th>
                        <th class="text-center">Total Bookings</th>
                        <th>Joined Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                <strong>{{ $customer->name }}</strong>
                            </td>
                            <td>
                                <small>
                                    <i class="fas fa-phone fa-sm text-muted"></i> {{ $customer->phone }}<br>
                                    <i class="fas fa-envelope fa-sm text-muted"></i> {{ $customer->email ?? 'N/A' }}
                                </small>
                            </td>
                            <td>
                                @if($customer->type == 'organization' && $customer->organization)
                                    <span class="badge bg-info text-dark">
                                        {{ $customer->organization->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Individual</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $customer->bookings_count }}</span>
                            </td>
                            <td>{{ $customer->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">No guests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer d-print-none bg-white border-0 mt-3">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>

        <div class="mt-5 text-end d-print-inline-block w-100" style="page-break-inside: avoid;">
            <p class="mb-5">Authorized Signature:</p>
            <p>___________________________</p>
        </div>
    </div>
</div>
@endsection
