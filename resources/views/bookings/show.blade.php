@extends('layouts.app')

@section('title', 'Manage Booking')
@section('page_title', 'Booking: ' . $booking->booking_number)

@section('content')
    <div class="row">
        <!-- Booking Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header border-bottom-0 pb-0 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Booking Details</h5>
                        <span
                            class="badge bg-{{ $booking->status == 'checked_in' ? 'success' : ($booking->status == 'checked_out' ? 'secondary' : 'primary') }} fs-6">
                            {{ mb_strtoupper(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted mb-1">Guest Information</h6>
                            <strong>{{ $booking->customer->name }}</strong><br>
                            <i class="fas fa-phone fa-fw text-muted"></i> {{ $booking->customer->phone }}<br>
                            @if($booking->customer->organization)
                                <i class="fas fa-building fa-fw text-muted"></i> {{ $booking->customer->organization->name }}
                            @endif
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <h6 class="text-muted mb-1">Room Information</h6>
                            <strong>Room {{ $booking->room->room_number }}</strong>
                            ({{ $booking->room->roomType->name }})<br>
                            {{ $booking->adults }} Adults, {{ $booking->children }} Children<br>
                            GH₵{{ number_format($booking->room_charge, 2) }} Total Room Charge
                        </div>
                    </div>

                    <div class="row bg-light rounded p-3 mb-4">
                        <div class="col-6 col-md-3 border-end">
                            <small class="text-muted d-block">Check-in Date</small>
                            <strong>{{ $booking->check_in_date->format('M d, Y') }}</strong>
                        </div>
                        <div class="col-6 col-md-3 border-end">
                            <small class="text-muted d-block">Check-out Date</small>
                            <strong>{{ $booking->check_out_date->format('M d, Y') }}</strong>
                        </div>
                        <div class="col-6 col-md-3 border-end mt-3 mt-md-0">
                            <small class="text-muted d-block">Duration</small>
                            <strong>{{ $booking->nights }} Night(s)</strong>
                        </div>
                        <div class="col-6 col-md-3 mt-3 mt-md-0">
                            <small class="text-muted d-block">Created By</small>
                            <strong>{{ $booking->createdBy->name ?? 'System' }}</strong>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        @if($booking->status == 'reserved')
                            <form action="{{ route('bookings.check-in', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="fas fa-sign-in-alt"></i> Check In
                                    Guest</button>
                            </form>
                        @elseif($booking->status == 'checked_in')
                            <form action="{{ route('bookings.check-out', $booking) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to check out this guest? This will generate their final bill and mark the room for cleaning.');">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-sign-out-alt"></i> Check Out Guest (Generate Bill)
                                </button>
                            </form>
                            <a href="{{ route('kitchen.create', ['booking_id' => $booking->id]) }}"
                                class="btn btn-outline-primary"><i class="fas fa-utensils"></i> Add Kitchen Order</a>
                            <a href="{{ route('laundry.create', ['booking_id' => $booking->id]) }}"
                                class="btn btn-outline-info"><i class="fas fa-tshirt"></i> Add Laundry</a>
                            <a href="{{ route('bar.create', ['booking_id' => $booking->id]) }}"
                                class="btn btn-outline-warning"><i class="fas fa-glass-martini-alt"></i> Add Bar Order</a>
                        @endif

                        @if($booking->bill)
                            <a href="{{ route('bills.show', $booking->bill) }}" class="btn btn-primary"><i
                                    class="fas fa-file-invoice"></i> View Bill</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Charges Summary -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Unbilled Services</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Accommodation ({{ $booking->nights }} nights)
                            <span>GH₵{{ number_format($booking->room_charge, 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Kitchen Orders ({{ $booking->kitchenOrders->where('status', '!=', 'cancelled')->count() }})
                            <span>GH₵{{ number_format($booking->kitchenOrders->where('status', '!=', 'cancelled')->sum('total_amount'), 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Laundry Orders ({{ $booking->laundryOrders->where('status', '!=', 'cancelled')->count() }})
                            <span>GH₵{{ number_format($booking->laundryOrders->where('status', '!=', 'cancelled')->sum('total_amount'), 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-bottom">
                            Bar Orders ({{ $booking->barOrders->where('status', '!=', 'cancelled')->count() }})
                            <span>GH₵{{ number_format($booking->barOrders->where('status', '!=', 'cancelled')->sum('total_amount'), 2) }}</span>
                        </li>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold fs-5 border-0 pt-3">
                            Total Accrued
                            @php
                                $totalAccrued = $booking->room_charge +
                                    $booking->kitchenOrders->where('status', '!=', 'cancelled')->sum('total_amount') +
                                    $booking->laundryOrders->where('status', '!=', 'cancelled')->sum('total_amount') +
                                    $booking->barOrders->where('status', '!=', 'cancelled')->sum('total_amount');
                            @endphp
                            <span>GH₵{{ number_format($totalAccrued, 2) }}</span>
                        </li>
                    </ul>
                    <div class="text-muted small text-center">
                        * The final bill may vary based on added services and discounts.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection