@extends('layouts.app')

@section('title', 'Add Bar Item')
@section('page_title', 'New Bar Item')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Item Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('bar-items.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required
                                placeholder="e.g. Club Beer, Red Wine">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description"
                                class="form-control @error('description') is-invalid @enderror"
                                rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Unit Price (GH₵) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price" id="price"
                                    class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}"
                                    required min="0">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="stock_quantity" class="form-label">Initial Stock Level <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="stock_quantity" id="stock_quantity"
                                    class="form-control @error('stock_quantity') is-invalid @enderror"
                                    value="{{ old('stock_quantity', 0) }}" required min="0">
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="expiry_date" class="form-label">
                                <i class="fas fa-calendar-times me-1 text-warning"></i>Expiry Date
                                <small class="text-muted">(optional)</small>
                            </label>
                            <input type="date" name="expiry_date" id="expiry_date"
                                class="form-control @error('expiry_date') is-invalid @enderror"
                                value="{{ old('expiry_date') }}">
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">Leave blank if this item does not expire.</div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Available for Sale</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('bar-items.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">Save Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection