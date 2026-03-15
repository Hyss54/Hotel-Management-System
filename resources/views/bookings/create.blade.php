@extends('layouts.app')

@section('title', 'New Booking & Check-in')
@section('page_title', 'Create Booking')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('bookings.store') }}" method="POST">
                @csrf

                <div class="row border-bottom pb-4 mb-4">
                    <div class="col-md-12">
                        <h5 class="text-primary mb-3"><i class="fas fa-user me-2"></i> Guest Information</h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guest *</label>
                        <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">Select Guest...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->phone }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted d-block mt-1">
                            Guest not listed? <a href="{{ route('customers.create') }}" target="_blank">Register new
                                guest</a> and refresh this page.
                        </small>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Number of Adults *</label>
                        <input type="number" name="adults" class="form-control @error('adults') is-invalid @enderror"
                            value="{{ old('adults', 1) }}" min="1" required>
                        @error('adults')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Number of Children</label>
                        <input type="number" name="children" class="form-control @error('children') is-invalid @enderror"
                            value="{{ old('children', 0) }}" min="0">
                        @error('children')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row pb-4 mb-4">
                    <div class="col-md-12">
                        <h5 class="text-primary mb-3"><i class="fas fa-bed me-2"></i> Stay Information</h5>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Check-in Date *</label>
                        <input type="date" name="check_in_date" id="checkin"
                            class="form-control @error('check_in_date') is-invalid @enderror"
                            value="{{ old('check_in_date', date('Y-m-d')) }}" required>
                        @error('check_in_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Check-out Date *</label>
                        <input type="date" name="check_out_date" id="checkout"
                            class="form-control @error('check_out_date') is-invalid @enderror"
                            value="{{ old('check_out_date', date('Y-m-d', strtotime('+1 day'))) }}" required>
                        @error('check_out_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Assign Room *</label>
                        <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                            <option value="">Select an Available Room...</option>
                            @foreach($roomTypes as $type)
                                <optgroup label="{{ $type->name }} (GH₵{{ number_format($type->price_per_night, 2) }}/night)">
                                    @foreach($rooms->where('room_type_id', $type->id) as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                            Room {{ $room->room_number }} - Floor {{ $room->floor }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row border-bottom pb-4 mb-4">
                    <div class="col-md-12">
                        <h5 class="text-primary mb-3"><i class="fas fa-clipboard-list me-2"></i> Additional Details</h5>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Special Requests / Notes</label>
                        <textarea name="special_requests"
                            class="form-control @error('special_requests') is-invalid @enderror"
                            rows="2">{{ old('special_requests') }}</textarea>
                        @error('special_requests')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" name="check_in_now"
                                id="checkInNow" value="1" {{ old('check_in_now') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-success" for="checkInNow">Check In Guest
                                Immediately</label>
                            <br><small class="text-muted">If unchecked, the booking status will be saved as
                                'Reserved'.</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save
                            Booking</button>
                        <a href="{{ route('bookings.index') }}" class="btn btn-light btn-lg ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Simple validation to ensure checkout is after checkin
            document.getElementById('checkin').addEventListener('change', function () {
                let checkinDate = new Date(this.value);
                let checkoutInput = document.getElementById('checkout');
                let checkoutDate = new Date(checkoutInput.value);

                if (checkinDate >= checkoutDate) {
                    checkinDate.setDate(checkinDate.getDate() + 1);
                    checkoutInput.value = checkinDate.toISOString().split('T')[0];
                }
                checkoutInput.min = checkinDate.toISOString().split('T')[0];
            });
        </script>
    @endpush
@endsection