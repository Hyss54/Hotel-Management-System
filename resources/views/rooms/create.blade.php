@extends('layouts.app')

@section('title', 'Add Room')
@section('page_title', 'Add New Room')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('rooms.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Number *</label>
                        <input type="text" name="room_number" class="form-control" value="{{ old('room_number') }}"
                            required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room Type *</label>
                        <select name="room_type_id" class="form-select" required>
                            <option value="">Select Type...</option>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ old('room_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Floor *</label>
                        <input type="number" name="floor" class="form-control" value="{{ old('floor', 1) }}" required
                            min="1">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                                checked>
                            <label class="form-check-label" for="isActive">Room is Active</label>
                        </div>
                    </div>
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">Save Room</button>
                <a href="{{ route('rooms.index') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
@endsection