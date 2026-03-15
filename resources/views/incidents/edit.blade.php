@extends('layouts.app')

@section('title', 'Edit Incident')
@section('page_title', 'Edit Incident: ' . $incident->title)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('incidents.update', $incident) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6 text-center mb-3">
                                <label class="form-label d-block">Incident Severity</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="severity" id="sev_low" value="low"
                                        autocomplete="off" {{ old('severity', $incident->severity) == 'low' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info" for="sev_low">Low</label>

                                    <input type="radio" class="btn-check" name="severity" id="sev_medium" value="medium"
                                        autocomplete="off" {{ old('severity', $incident->severity) == 'medium' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="sev_medium">Medium</label>

                                    <input type="radio" class="btn-check" name="severity" id="sev_high" value="high"
                                        autocomplete="off" {{ old('severity', $incident->severity) == 'high' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning" for="sev_high">High</label>

                                    <input type="radio" class="btn-check" name="severity" id="sev_emergency"
                                        value="emergency" autocomplete="off" {{ old('severity', $incident->severity) == 'emergency' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger" for="sev_emergency">Emergency</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Guest / Customer *</label>
                                <select name="customer_id" class="form-select" required>
                                    <option value="">Select Guest...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $incident->customer_id) == $customer->id ? 'selected' : '' }}>
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
                                        <option value="{{ $room->id }}" {{ old('room_id', $incident->room_id) == $room->id ? 'selected' : '' }}>
                                            Room {{ $room->room_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Booking ID (Optional)</label>
                                <input type="text" name="booking_id" class="form-control"
                                    value="{{ old('booking_id', $incident->booking_id) }}" placeholder="e.g. 1">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Incident Title *</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="e.g., Guest Lost Key" value="{{ old('title', $incident->title) }}"
                                required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Detailed Description *</label>
                            <textarea name="description" class="form-control" rows="5"
                                required>{{ old('description', $incident->description) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary px-4">Update Incident Report</button>
                            <a href="{{ route('incidents.show', $incident) }}" class="btn btn-light ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
