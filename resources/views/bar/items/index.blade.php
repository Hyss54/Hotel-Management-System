@extends('layouts.app')

@section('title', 'Bar Inventory')
@section('page_title', 'Bar Stock Management')

@section('content')

    {{-- Expiry Alert Banner --}}
    @php
        $expiredCount     = $items->filter(fn($i) => $i->isExpired())->count();
        $expiringSoonCount = $items->filter(fn($i) => $i->isExpiringSoon())->count();
    @endphp

    @if($expiredCount > 0 || $expiringSoonCount > 0)
        <div class="alert alert-warning border-warning d-flex align-items-start gap-3 mb-4 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle fa-lg text-warning mt-1"></i>
            <div>
                <strong>Inventory Expiry Alert</strong><br>
                @if($expiredCount > 0)
                    <span class="me-3">
                        <span class="badge bg-danger me-1">{{ $expiredCount }}</span>
                        item{{ $expiredCount > 1 ? 's' : '' }} <strong>expired</strong> — remove from stock immediately.
                    </span>
                @endif
                @if($expiringSoonCount > 0)
                    <span>
                        <span class="badge bg-warning text-dark me-1">{{ $expiringSoonCount }}</span>
                        item{{ $expiringSoonCount > 1 ? 's' : '' }} expiring <strong>within 7 days</strong>.
                    </span>
                @endif
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Inventory Items</h5>
            @can('manage-bar-items')
            <a href="{{ route('bar-items.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Add New Item
            </a>
            @endcan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock Level</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr class="{{ $item->isExpired() ? 'table-danger' : ($item->isExpiringSoon() ? 'table-warning' : '') }}">
                                <td>
                                    <strong>{{ $item->name }}</strong>
                                </td>
                                <td><small class="text-muted">{{ Str::limit($item->description, 50) ?: 'No description' }}</small></td>
                                <td>GH₵{{ number_format($item->price, 2) }}</td>
                                <td>
                                    @if($item->stock_quantity <= 0)
                                        <span class="badge bg-danger rounded-pill">Out of Stock</span>
                                    @elseif($item->stock_quantity <= 5)
                                        <span class="badge bg-warning text-dark rounded-pill">{{ $item->stock_quantity }} (Low)</span>
                                    @else
                                        <span class="badge bg-success rounded-pill">{{ $item->stock_quantity }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->expiry_date === null)
                                        <span class="text-muted">—</span>
                                    @elseif($item->isExpired())
                                        <span class="badge bg-danger">
                                            <i class="fas fa-skull-crossbones me-1"></i>Expired
                                        </span>
                                        <br><small class="text-danger">{{ $item->expiry_date->format('d M Y') }}</small>
                                    @elseif($item->isExpiringSoon())
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-exclamation-circle me-1"></i>Expiring Soon
                                        </span>
                                        <br><small class="text-warning">{{ $item->expiry_date->format('d M Y') }}</small>
                                    @else
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-calendar-check me-1 text-success"></i>{{ $item->expiry_date->format('d M Y') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->is_active ? 'info' : 'secondary' }}">
                                        {{ $item->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group">
                                        <a href="{{ route('bar-items.show', $item) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('manage-bar-items')
                                        <a href="{{ route('bar-items.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit Item">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('bar-items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-beer fa-3x mb-3"></i><br>
                                    No items in stock. Start by adding some!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
