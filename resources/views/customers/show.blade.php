@extends('layouts.app')

@section('title', 'Customer Details - ' . $customer->name)
@section('page_title', 'Customer Profile')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <!-- Customer Info Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Personal Information</h5>
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=0D8ABC&color=fff&size=128"
                            class="rounded-circle shadow-sm mb-3" alt="{{ $customer->name }}">
                        <h4>{{ $customer->name }}</h4>
                        <span class="badge bg-{{ $customer->is_active ? 'success' : 'danger' }}">
                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="badge bg-secondary ms-1">
                            {{ ucfirst(str_replace('_', ' ', $customer->type)) }}
                        </span>
                    </div>

                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="fas fa-phone me-2"></i> Phone</span>
                            <strong>{{ $customer->phone }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="fas fa-envelope me-2"></i> Email</span>
                            <strong>{{ $customer->email ?? 'N/A' }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="fas fa-id-card me-2"></i> ID Type</span>
                            <strong>{{ strtoupper($customer->id_type ?? 'N/A') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted"><i class="fas fa-hashtag me-2"></i> ID Number</span>
                            <strong>{{ $customer->id_number ?? 'N/A' }}</strong>
                        </li>
                        @if($customer->type == 'organization_guest' && $customer->organization)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted"><i class="fas fa-building me-2"></i> Organization</span>
                                <span class="badge bg-info">{{ $customer->organization->name }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="card shadow-sm mb-4 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-exclamation-circle me-1"></i> Emergency Contact</h6>
                </div>
                <div class="card-body py-2">
                    <p class="mb-1"><strong>{{ $customer->emergency_contact_name ?? 'Not Set' }}</strong></p>
                    <p class="mb-0 text-muted small">{{ $customer->emergency_contact_phone ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Booking History -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i> Booking History</h5>
                    <span class="badge bg-primary rounded-pill">{{ $customer->bookings->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Booking #</th>
                                    <th>Room</th>
                                    <th>Dates</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->bookings as $booking)
                                    <tr>
                                        <td><strong>{{ $booking->booking_number }}</strong></td>
                                        <td>
                                            Room {{ $booking->room->room_number }}<br>
                                            <small class="text-muted">{{ $booking->room->roomType->name }}</small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $booking->check_in_date->format('M d, Y') }} -
                                                {{ $booking->check_out_date->format('M d, Y') }}<br>
                                                <span class="text-muted">({{ $booking->nights }} nights)</span>
                                            </small>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $booking->status == 'checked_in' ? 'success' : ($booking->status == 'reserved' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('bookings.show', $booking) }}"
                                                class="btn btn-xs btn-outline-info p-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No booking history available.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bills & Payments -->
            <div class="card shadow-sm mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i> Billing & Payments</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Bill #</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Payments</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->bills as $bill)
                                    <tr>
                                        <td><strong>{{ $bill->bill_number }}</strong></td>
                                        <td>GH₵{{ number_format($bill->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $bill->status == 'paid' ? 'success' : 'warning' }}">
                                                {{ ucfirst($bill->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-success">Paid:
                                                GH₵{{ number_format($bill->payments->sum('amount'), 2) }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('bills.show', $bill) }}"
                                                class="btn btn-xs btn-outline-success p-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No billing records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection