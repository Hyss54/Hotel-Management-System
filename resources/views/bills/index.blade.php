@extends('layouts.app')

@section('title', 'Billing & Payments')
@section('page_title', 'Billing')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Recent Bills</span>
            <a href="{{ route('bills.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Generate Bill
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Bill No.</th>
                            <th>Guest</th>
                            <th>Amount</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bills as $bill)
                            <tr>
                                <td><strong>{{ $bill->bill_number }}</strong><br><small
                                        class="text-muted">{{ $bill->issued_at->format('M d, Y') }}</small></td>
                                <td>{{ $bill->customer->name }}</td>
                                <td>GH₵{{ number_format($bill->total_amount, 2) }}</td>
                                <td class="{{ $bill->balance > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                    GH₵{{ number_format($bill->balance, 2) }}
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match ($bill->status) {
                                            'paid' => 'success',
                                            'partial' => 'warning',
                                            'issued' => 'primary',
                                            'cancelled' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($bill->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('bills.show', $bill) }}" class="btn btn-sm btn-primary"><i
                                            class="fas fa-eye"></i> View</a>
                                    <a href="{{ route('bills.invoice', $bill) }}" class="btn btn-sm btn-outline-secondary"><i
                                            class="fas fa-download"></i> PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No bills generated yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $bills->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection