@extends('layouts.app')

@section('title', 'Add Room Type')
@section('page_title', 'Configure New Room Type')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('room-types.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required
                            placeholder="e.g., Standard Double, Executive Suite">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Price Per Night *</label>
                        <div class="input-group">
                            <span class="input-group-text">GH₵</span>
                            <input type="number" name="price_per_night" class="form-control" min="0" step="0.01"
                                value="{{ old('price_per_night') }}" required>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Max. Guests *</label>
                        <input type="number" name="max_guests" class="form-control" min="1"
                            value="{{ old('max_guests', 2) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label">Amenities</label>
                        <input type="text" name="amenities" class="form-control" value="{{ old('amenities') }}"
                            placeholder="Comma-separated list (e.g., WiFi, TV, AC, Minibar)">
                        <small class="text-muted">Separate multiple amenities with commas</small>
                    </div>
                </div>

                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Room Type</button>
                    <a href="{{ route('room-types.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection