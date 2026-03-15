@extends('layouts.app')

@section('title', 'Bar Item Details')
@section('page_title', 'Bar Item: ' . $barItem->name)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Item Information</h5>
                    <a href="{{ route('bar-items.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Inventory
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Item Name</label>
                            <p class="fs-5 fw-bold text-primary">{{ $barItem->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small text-uppercase fw-bold">Status</label>
                            <div>
                                <span class="badge bg-{{ $barItem->is_active ? 'success' : 'secondary' }} fs-6">
                                    {{ $barItem->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="text-muted small text-uppercase fw-bold">Description</label>
                            <p class="text-dark">{{ $barItem->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold">Price</label>
                            <p class="fs-5">GH₵{{ number_format($barItem->price, 2) }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold">Stock Level</label>
                            <div>
                                @if($barItem->stock_quantity <= 0)
                                    <span class="badge bg-danger rounded-pill px-3">Out of Stock</span>
                                @elseif($barItem->stock_quantity <= 5)
                                    <span class="badge bg-warning text-dark rounded-pill px-3">{{ $barItem->stock_quantity }}
                                        (Low)</span>
                                @else
                                    <span class="badge bg-success rounded-pill px-3">{{ $barItem->stock_quantity }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small text-uppercase fw-bold">Expiry Date</label>
                            <p>
                                @if($barItem->expiry_date === null)
                                    <span class="text-muted">No expiry date</span>
                                @else
                                    <span
                                        class="{{ $barItem->isExpired() ? 'text-danger fw-bold' : ($barItem->isExpiringSoon() ? 'text-warning fw-bold' : 'text-dark') }}">
                                        {{ $barItem->expiry_date->format('d M Y') }}
                                        @if($barItem->isExpired())
                                            <span class="ms-1 small">(Expired)</span>
                                        @elseif($barItem->isExpiringSoon())
                                            <span class="ms-1 small">(Expiring Soon)</span>
                                        @endif
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @can('manage-bar-items')
                    <div class="card-footer bg-light d-flex justify-content-end gap-2">
                        <a href="{{ route('bar-items.edit', $barItem) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Item
                        </a>
                        <form action="{{ route('bar-items.destroy', $barItem) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                @endcan
            </div>
        </div>
    </div>
@endsection