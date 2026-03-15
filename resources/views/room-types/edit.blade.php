@extends('layouts.app')

@section('title', 'Edit Room Type')
@section('page_title', 'Editing: ' . $roomType->name)

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('room-types.update', $roomType) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $roomType->name) }}"
                            required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Price Per Night *</label>
                        <div class="input-group">
                            <span class="input-group-text">GH₵</span>
                            <input type="number" name="price_per_night" class="form-control" min="0" step="0.01"
                                value="{{ old('price_per_night', $roomType->price_per_night) }}" required>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Max. Guests *</label>
                        <input type="number" name="max_guests" class="form-control" min="1"
                            value="{{ old('max_guests', $roomType->max_guests) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"
                            rows="3">{{ old('description', $roomType->description) }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label">Amenities</label>
                        <input type="text" name="amenities" class="form-control"
                            value="{{ old('amenities', implode(', ', (array) $roomType->amenities)) }}"
                            placeholder="Comma-separated list (e.g., WiFi, TV, AC, Minibar)">
                        <small class="text-muted">Separate multiple amenities with commas</small>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                        <a href="{{ route('room-types.index') }}" class="btn btn-light ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection