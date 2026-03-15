@extends('layouts.app')

@section('title', 'Invoice ' . $bill->bill_number)
@section('page_title', 'Bill Details: ' . $bill->bill_number)

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4" id="invoice">
                <div class="card-body p-5">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h3 class="fw-bold mb-1" style="color: var(--accent-color);">Gold Plus Hotel</h3>
                            <div>123 Luxury Avenue, Metropolis</div>
                            <div>Phone: +1 234 567 890</div>
                            <div>Email: contact@goldplushotel.com</div>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <h4 class="text-primary mb-1">INVOICE</h4>
                            <div><strong>Invoice No:</strong> {{ $bill->bill_number }}</div>
                            <div><strong>Date:</strong> {{ $bill->issued_at->format('M d, Y') }}</div>
                            <div><strong>Status:</strong> <span
                                    class="badge bg-{{ $bill->status == 'paid' ? 'success' : ($bill->status == 'partial' ? 'warning' : 'primary') }} fs-6">{{ strtoupper($bill->status) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-sm-6">
                            <h6 class="text-muted mb-2">Billed To:</h6>
                            <strong>{{ $bill->customer->name }}</strong><br>
                            @if($bill->customer->organization)
                                {{ $bill->customer->organization->name }}<br>
                            @endif
                            {{ $bill->customer->phone }}<br>
                            {{ $bill->customer->email ?? '' }}
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            @if($bill->booking)
                                <h6 class="text-muted mb-2">Stay Details:</h6>
                                <strong>Room {{ $bill->booking->room->room_number }}</strong>
                                ({{ $bill->booking->room->roomType->name }})<br>
                                Check-in: {{ $bill->booking->check_in_date->format('M d, Y') }}<br>
                                Check-out: {{ $bill->booking->check_out_date->format('M d, Y') }}<br>
                                Duration: {{ $bill->booking->nights }} Night(s)
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($bill->room_charge > 0)
                                    <tr>
                                        <td>Accommodation Charge
                                            {{ $bill->booking ? '(' . $bill->booking->nights . ' nights)' : '' }}</td>
                                        <td class="text-end">GH₵{{ number_format($bill->room_charge, 2) }}</td>
                                    </tr>
                                @endif
                                @if($bill->kitchen_charge > 0)
                                    <tr>
                                        <td>Restaurant & Kitchen Orders</td>
                                        <td class="text-end">GH₵{{ number_format($bill->kitchen_charge, 2) }}</td>
                                    </tr>
                                @endif
                                @if($bill->laundry_charge > 0)
                                    <tr>
                                        <td>Laundry Services</td>
                                        <td class="text-end">GH₵{{ number_format($bill->laundry_charge, 2) }}</td>
                                    </tr>
                                @endif
                                @if($bill->bar_charge > 0)
                                    <tr>
                                        <td>Bar & Drink Orders</td>
                                        <td class="text-end">GH₵{{ number_format($bill->bar_charge, 2) }}</td>
                                    </tr>
                                @endif
                                @if($bill->other_charges > 0)
                                    <tr>
                                        <td>Other Services / Charges</td>
                                        <td class="text-end">GH₵{{ number_format($bill->other_charges, 2) }}</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="border-top-0">
                                <tr>
                                    <td class="text-end text-muted pb-1 pt-3">Subtotal:</td>
                                    <td class="text-end pb-1 pt-3">
                                        GH₵{{ number_format($bill->total_amount + $bill->discount, 2) }}</td>
                                </tr>
                                @if($bill->discount > 0)
                                    <tr>
                                        <td class="text-end text-muted py-1 border-0">Discount:</td>
                                        <td class="text-end text-success py-1 border-0">
                                            -GH₵{{ number_format($bill->discount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="text-end fw-bold pb-2 pt-1 border-0 fs-5">Total Amount:</td>
                                    <td class="text-end fw-bold pb-2 pt-1 border-0 fs-5">
                                        GH₵{{ number_format($bill->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end text-muted py-1">Amount Paid:</td>
                                    <td class="text-end py-1">GH₵{{ number_format($bill->paid_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-bold pt-2 text-danger fs-5 bg-light rounded-start">Balance Due:
                                    </td>
                                    <td class="text-end fw-bold pt-2 text-danger fs-5 bg-light rounded-end">
                                        GH₵{{ number_format($bill->balance, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($bill->notes)
                        <div class="text-muted small"><strong>Notes:</strong> {{ $bill->notes }}</div>
                    @endif
                    <div class="text-center text-muted mt-5 pt-3 border-top small">
                        Thank you for staying at Gold Plus Hotel. We hope to see you again soon!
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="col-lg-4">
            <!-- Actions -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <a href="{{ route('bills.invoice', $bill) }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="fas fa-download"></i> Download PDF Invoice
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-print"></i> Print View
                    </button>
                </div>
            </div>

            @if($bill->balance > 0)
                <!-- Record Payment block -->
                <div class="card">
                    <div class="card-header bg-success text-white">Record Payment</div>
                    <div class="card-body">
                        <form action="{{ route('bills.payment', $bill) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Payment Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">GH₵</span>
                                    <input type="number" name="amount" class="form-control" step="0.01"
                                        max="{{ $bill->balance }}" value="{{ $bill->balance }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select name="method" class="form-select" required>
                                    <option value="cash">Cash</option>
                                    <option value="card">Credit/Debit Card</option>
                                    <option value="mobile_money">Mobile Money</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Transaction ID (Optional)</label>
                                <input type="text" name="transaction_id" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Submit Payment</button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Payment History -->
            @if($bill->payments->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">Payment History</div>
                    <ul class="list-group list-group-flush">
                        @foreach($bill->payments as $payment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>GH₵{{ number_format($payment->amount, 2) }}</strong><br>
                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $payment->method)) }}</small>
                                </div>
                                <div class="text-end">
                                    <small class="d-block text-muted">{{ $payment->paid_at->format('M d, H:i') }}</small>
                                    <small class="text-muted" style="font-size: 0.7em;">Ref: {{ $payment->reference }}</small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection