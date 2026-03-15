@extends('layouts.app')

@section('title', 'Edit Room')
@section('page_title', 'Edit Room: ' . $room->room_number)

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('rooms.update', $room) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Number *</label>
                        <input type="text" name="room_number" class="form-control"
                            value="{{ old('room_number', $room->room_number) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Type *</label>
                        <select name="room_type_id" class="form-select" required>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ old('room_type_id', $room->room_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Floor *</label>
                        <input type="number" name="floor" class="form-control" value="{{ old('floor', $room->floor) }}"
                            required min="1">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>
                                Available</option>
                            <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>
                                Occupied</option>
                            <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>
                                Maintenance</option>
                            <option value="reserved" {{ old('status', $room->status) == 'reserved' ? 'selected' : '' }}>
                                Reserved</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $room->notes) }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ old('is_active', $room->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Room is Active</label>
                        </div>
                    </div>
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">Update Room</button>
                <a href="{{ route('rooms.index') }}" class="btn btn-light ms-2">Cancel</a>
            </form>
        </div>
    </div>
@endsection