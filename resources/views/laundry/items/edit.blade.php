@extends('layouts.app')

@section('title', 'Edit Laundry Item')
@section('page_title', 'Edit Inventory Item')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Edit Item: {{ $laundryItem->name }}</div>
            <div class="card-body">
                <form action="{{ route('laundry-items.update', $laundryItem) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="name" class="form-label">Item Name</label>
                        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $laundryItem->name) }}">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $laundryItem->description) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="stock_quantity" class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" required min="0" value="{{ old('stock_quantity', $laundryItem->stock_quantity) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">Unit (e.g., pcs, kg, liters)</label>
                            <input type="text" name="unit" id="unit" class="form-control" required value="{{ old('unit', $laundryItem->unit) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="min_stock_level" class="form-label">Min. Stock level (for alerts)</label>
                            <input type="number" name="min_stock_level" id="min_stock_level" class="form-control" required min="0" value="{{ old('min_stock_level', $laundryItem->min_stock_level) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="is_active" id="active" value="1" {{ $laundryItem->is_active ? 'checked' : '' }}>
                                <label class="form-check-input-label" for="active">Active</label>
                            </div>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="radio" name="is_active" id="inactive" value="0" {{ !$laundryItem->is_active ? 'checked' : '' }}>
                                <label class="form-check-input-label" for="inactive">Inactive</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('laundry-items.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
