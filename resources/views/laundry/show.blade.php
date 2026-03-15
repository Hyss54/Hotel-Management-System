@extends('layouts.app')

@section('title', 'Manage Laundry Order')
@section('page_title', 'Order: ' . $laundry->order_number)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header border-bottom-0 pb-0 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Laundry Order Details</h5>
                        <span
                            class="badge bg-{{ $laundry->status == 'delivered' ? 'success' : ($laundry->status == 'cancelled' ? 'danger' : 'primary') }} fs-6">
                            {{ mb_strtoupper(str_replace('_', ' ', $laundry->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted mb-1">Guest Information</h6>
                            <strong>{{ $laundry->customer->name }}</strong><br>
                            <i class="fas fa-phone fa-fw text-muted"></i> {{ $laundry->customer->phone }}
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <h6 class="text-muted mb-1">Booking Information</h6>
                            @if($laundry->booking)
                                <strong>Room {{ $laundry->booking->room->room_number }}</strong><br>
                                Booking: {{ $laundry->booking->booking_number }}
                            @else
                                <span class="text-muted">Walk-in Customer (No Booking)</span>
                            @endif
                        </div>
                    </div>

                    <div class="bg-light rounded p-3 mb-4">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <small class="text-muted d-block">Created At</small>
                                <strong>{{ $laundry->created_at->format('M d, Y - H:i') }}</strong>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0 pl-md-3">
                                <small class="text-muted d-block">Expected Delivery</small>
                                <strong>{{ $laundry->due_date ? $laundry->due_date->format('M d, Y - H:i') : 'Not Specified' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">Order Details / Requests</h6>
                        <div class="p-3 border rounded bg-white">
                            {{ $laundry->notes }}
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <form action="{{ route('laundry.updateStatus', $laundry) }}" method="POST"
                            class="d-flex gap-2 align-items-center">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select w-auto">
                                <option value="pending" {{ $laundry->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $laundry->status == 'processing' ? 'selected' : '' }}>Processing
                                </option>
                                <option value="ready" {{ $laundry->status == 'ready' ? 'selected' : '' }}>Ready</option>
                                <option value="delivered" {{ $laundry->status == 'delivered' ? 'selected' : '' }}>Delivered
                                </option>
                                <option value="cancelled" {{ $laundry->status == 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charges Summary -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">Billing Information</div>
                <div class="card-body">
                    <h4 class="text-center text-success mb-3">GH₵{{ number_format($laundry->total_amount, 2) }}</h4>
                    @if($laundry->booking)
                        <div class="alert alert-info py-2 small mb-0">
                            <i class="fas fa-info-circle"></i> This charge will be automatically added to the guest's final
                            check-out bill.
                        </div>
                    @else
                        <div class="alert alert-warning py-2 small mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Walk-in order. Ensure payment is collected directly.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection