@extends('layouts.app')

@section('title', 'Generate New Bill')
@section('page_title', 'Create Bill')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('bills.store') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Customer *</label>
                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">Select Customer...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->phone }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Link to Checked-out Booking (Optional)</label>
                        <select name="booking_id" class="form-select @error('booking_id') is-invalid @enderror">
                            <option value="">No Booking (Manual Bill)</option>
                            @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                                    Room {{ $booking->room->room_number }} - {{ $booking->customer->name }}
                                    ({{ $booking->check_out_date->format('M d') }})
                                </option>
                            @endforeach
                        </select>
                        @error('booking_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">If selected, Accommodation, Kitchen, and Laundry charges will be
                            automatically calculated.</small>
                    </div>
                </div>

                <div class="row mb-4 bg-light p-3 rounded border">
                    <div class="col-md-12 mb-3">
                        <h5 class="text-primary mb-0">Manual Charges</h5>
                        <small class="text-muted border-bottom d-block pb-2 mb-3">Fill this section only if you are creating
                            a manual bill not linked to a booking.</small>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Accommodation Charge</label>
                        <div class="input-group">
                            <span class="input-group-text">GH₵</span>
                            <input type="number" name="room_charge" class="form-control" step="0.01" min="0"
                                value="{{ old('room_charge') }}">
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Kitchen Charge</label>
                        <div class="input-group">
                            <span class="input-group-text">GH₵</span>
                            <input type="number" name="kitchen_charge" class="form-control" step="0.01" min="0"
                                value="{{ old('kitchen_charge') }}">
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Laundry Charge</label>
                        <div class="input-group">
                            <span class="input-group-text">GH₵</span>
                            <input type="number" name="laundry_charge" class="form-control" step="0.01" min="0"
                                value="{{ old('laundry_charge') }}">
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Other Charges</label>
                        <div class="input-group">
                            <span class="input-group-text">GH₵</span>
                            <input type="number" name="other_charges" class="form-control" step="0.01" min="0"
                                value="{{ old('other_charges') }}">
                        </div>
                    </div>
                </div>

                <div class="row pb-4 mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Discount Applied (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text">GH₵</span>
                            <input type="number" name="discount"
                                class="form-control @error('discount') is-invalid @enderror" step="0.01" min="0"
                                value="{{ old('discount', 0) }}">
                        </div>
                        @error('discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-8 mb-3">
                        <label class="form-label">Bill Notes</label>
                        <textarea name="notes" class="form-control" rows="2"
                            placeholder="Any manual breakdown or descriptions for this bill...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-file-invoice dollar"></i>
                            Generate Bill</button>
                        <a href="{{ route('bills.index') }}" class="btn btn-light btn-lg ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection