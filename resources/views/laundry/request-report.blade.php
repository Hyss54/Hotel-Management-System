@extends('layouts.app')

@section('title', 'Inventory Request Report')
@section('page_title', 'Inventory Request Report')

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
        .badge {
            border: 1px solid #333;
            padding: 2px 6px;
        }
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center d-print-none">
        <span>Report Filters</span>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>
    <div class="card-body bg-light border-bottom d-print-none">
        <form method="GET" class="row gx-2 gy-2 align-items-center">
            <div class="col-auto">
                <label class="form-label small mb-0">From</label>
                <input type="date" class="form-control form-control-sm" name="from_date" value="{{ request('from_date') }}">
            </div>
            <div class="col-auto">
                <label class="form-label small mb-0">To</label>
                <input type="date" class="form-control form-control-sm" name="to_date" value="{{ request('to_date') }}">
            </div>
            <div class="col-auto">
                <label class="form-label small mb-0">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="clean_delivery" {{ request('type') == 'clean_delivery' ? 'selected' : '' }}>Request</option>
                    <option value="dirty_collection" {{ request('type') == 'dirty_collection' ? 'selected' : '' }}>Return</option>
                    <option value="wash_complete" {{ request('type') == 'wash_complete' ? 'selected' : '' }}>Wash Complete</option>
                    <option value="restock" {{ request('type') == 'restock' ? 'selected' : '' }}>Restock</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small mb-0">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="denied" {{ request('status') == 'denied' ? 'selected' : '' }}>Denied</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-sm btn-secondary">Generate Report</button>
            </div>
        </form>
    </div>

    <div class="card-body" id="print-area">
        <div class="report-header text-center mb-4 pb-3 border-bottom">
            <h2 class="fw-bold mb-1">{{ config('app.name', 'Gold Plus Hotel') }}</h2>
            <h4 class="text-uppercase mb-2">Inventory Request Report</h4>
            @if(request('from_date') || request('to_date'))
                <p class="mb-0 text-muted">
                    <strong>Period:</strong>
                    {{ request('from_date', 'Start') }} — {{ request('to_date', 'Present') }}
                </p>
            @endif
            <p class="mb-0 text-muted small">Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="border rounded p-3 bg-light text-center">
                    <h6 class="text-uppercase text-muted mb-1">Total Transactions</h6>
                    <h3 class="mb-0">{{ $transactions->count() }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 bg-light text-center">
                    <h6 class="text-uppercase text-muted mb-1">Pending</h6>
                    <h3 class="mb-0 text-warning">{{ $transactions->where('status', 'pending')->count() }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 bg-light text-center">
                    <h6 class="text-uppercase text-muted mb-1">Approved/Completed</h6>
                    <h3 class="mb-0 text-success">{{ $transactions->whereIn('status', ['approved', 'completed'])->count() }}</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="border rounded p-3 bg-light text-center">
                    <h6 class="text-uppercase text-muted mb-1">Denied</h6>
                    <h3 class="mb-0 text-danger">{{ $transactions->where('status', 'denied')->count() }}</h3>
                </div>
            </div>
        </div>

        {{-- Transaction Table --}}
        <h5 class="mb-3">Transaction Details</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date & Time</th>
                        <th>Type</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Status</th>
                        <th>Requested By</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $txn)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="small">{{ $txn->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @php
                                    $typeLabel = match($txn->transaction_type) {
                                        'clean_delivery' => 'Request',
                                        'dirty_collection' => 'Return',
                                        'wash_complete' => 'Wash Complete',
                                        'restock' => 'Restock',
                                        default => ucfirst(str_replace('_', ' ', $txn->transaction_type))
                                    };
                                    $typeBadge = match($txn->transaction_type) {
                                        'clean_delivery' => 'success',
                                        'dirty_collection' => 'warning',
                                        'wash_complete' => 'info',
                                        'restock' => 'primary',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $typeBadge }}">{{ $typeLabel }}</span>
                            </td>
                            <td>{{ $txn->item->name ?? 'N/A' }}</td>
                            <td>{{ $txn->quantity }}</td>
                            <td class="small">{{ $txn->fromDepartment->name ?? '—' }}</td>
                            <td class="small">{{ $txn->toDepartment->name ?? '—' }}</td>
                            <td>
                                @php
                                    $statusBadge = match($txn->status) {
                                        'pending' => 'warning',
                                        'approved', 'completed' => 'success',
                                        'denied' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusBadge }}">{{ ucfirst($txn->status) }}</span>
                            </td>
                            <td class="small">{{ $txn->creator->name ?? 'System' }}</td>
                            <td class="small text-muted">{{ Str::limit($txn->notes, 40) ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">No transactions found for the selected filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5 text-end d-print-inline-block w-100" style="page-break-inside: avoid;">
            <p class="mb-5">Authorized Signature:</p>
            <p>___________________________</p>
        </div>
    </div>
</div>
@endsection
