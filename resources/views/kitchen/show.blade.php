@extends('layouts.app')

@section('title', 'Manage Kitchen Order')
@section('page_title', 'Kitchen Order: ' . $kitchen->order_number)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header border-bottom-0 pb-0 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Kitchen Order Details</h5>
                        <span
                            class="badge bg-{{ $kitchen->status == 'served' ? 'success' : ($kitchen->status == 'cancelled' ? 'danger' : 'warning') }} fs-6">
                            {{ mb_strtoupper(str_replace('_', ' ', $kitchen->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted mb-1">Guest Information</h6>
                            <strong>{{ $kitchen->customer->name }}</strong><br>
                            <i class="fas fa-phone fa-fw text-muted"></i> {{ $kitchen->customer->phone }}
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <h6 class="text-muted mb-1">Location Details</h6>
                            @if($kitchen->booking)
                                <strong>Room {{ $kitchen->booking->room->room_number }}</strong><br>
                                Booking: {{ $kitchen->booking->booking_number }}
                            @else
                                <span class="text-muted">Restaurant / Walk-in</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-light rounded p-3 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <small class="text-muted d-block">Order Placed At</small>
                                <strong>{{ $kitchen->created_at->format('M d, Y - H:i') }}</strong>
                                <small class="text-muted ms-3">({{ $kitchen->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">Order Details</h6>
                        <div class="p-3 border rounded bg-white">
                            {!! nl2br(e($kitchen->notes)) !!}
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <form action="{{ route('kitchen.updateStatus', $kitchen) }}" method="POST"
                            class="d-flex gap-2 align-items-center">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select w-auto">
                                <option value="pending" {{ $kitchen->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="preparing" {{ $kitchen->status == 'preparing' ? 'selected' : '' }}>Preparing
                                </option>
                                <option value="served" {{ $kitchen->status == 'served' ? 'selected' : '' }}>Served</option>
                                <option value="cancelled" {{ $kitchen->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                            <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt"></i> Update
                                Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charges Summary -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">Billing Information</div>
                <div class="card-body">
                    <h4 class="text-center text-dark mb-3">GH₵{{ number_format($kitchen->total_amount, 2) }}</h4>
                    @if($kitchen->booking)
                        <div class="alert alert-info py-2 small mb-0">
                            <i class="fas fa-info-circle"></i> This charge will be logged to Room
                            {{ $kitchen->booking->room->room_number }}'s final bill.
                        </div>
                    @else
                        <div class="alert alert-warning py-2 small mb-0">
                            <i class="fas fa-cash-register"></i> Walk-in order. Ensure payment is collected at the restaurant.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection