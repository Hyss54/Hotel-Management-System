@extends('layouts.app')

@section('title', 'Log Incident')
@section('page_title', 'New Incident Report')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('incidents.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6 text-center mb-3">
                                <label class="form-label d-block">Incident Severity</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="severity" id="sev_low" value="low"
                                        autocomplete="off" {{ old('severity') == 'low' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="sev_low">Low</label>

                                    <input type="radio" class="btn-check" name="severity" id="sev_medium" value="medium"
                                        autocomplete="off" {{ old('severity', 'medium') == 'medium' ? 'checked' : 'checked' }}>
                                    <label class="btn btn-outline-primary" for="sev_medium">Medium</label>

                                    <input type="radio" class="btn-check" name="severity" id="sev_high" value="high"
                                        autocomplete="off" {{ old('severity') == 'high' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning" for="sev_high">High</label>

                                    <input type="radio" class="btn-check" name="severity" id="sev_emergency"
                                        value="emergency" autocomplete="off" {{ old('severity') == 'emergency' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger" for="sev_emergency">Emergency</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Guest / Customer *</label>
                                <select name="customer_id" class="form-select" required>
                                    <option value="">Select Guest...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $selected_customer?->id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} ({{ $customer->phone }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Room (Optional)</label>
                                <select name="room_id" class="form-select">
                                    <option value="">No specific room</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id', $selected_room?->id) == $room->id ? 'selected' : '' }}>
                                            Room {{ $room->room_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Booking ID (Optional)</label>
                                <input type="text" name="booking_id" class="form-control"
                                    value="{{ old('booking_id', $selected_booking?->id) }}" placeholder="e.g. 1">
                                <small class="text-muted">Links this incident to a specific stay.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Incident Title *</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="e.g., Guest Lost Key, Faulty AC, Medical Assistance" value="{{ old('title') }}"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Detailed Description *</label>
                            <textarea name="description" class="form-control" rows="5"
                                placeholder="Please provide as much detail as possible about what happened and what steps were taken..."
                                required>{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-start">
                            <button type="submit" class="btn btn-danger px-4">Log Incident Report</button>
                            <a href="{{ route('frontdesk.index') }}" class="btn btn-light ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection