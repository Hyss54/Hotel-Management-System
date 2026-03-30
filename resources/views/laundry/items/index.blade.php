@extends('layouts.app')

@section('title', 'Laundry Inventory')
@section('page_title', 'Laundry Inventory')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Inventory Items</span>
        <div>
            <a href="{{ route('inventory-sync.request-report') }}" class="btn btn-sm btn-info me-2 text-white">
                <i class="fas fa-file-alt"></i> Request Report
            </a>
            @can('manage-laundry-items')
                <a href="{{ route('laundry-items.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add New Item
                </a>
            @endcan
        </div>
    </div>

    <div class="card-body border-bottom bg-light">
        <form method="GET" class="row gx-2 gy-2 align-items-center">
            <div class="col-auto">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search items..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Available Stock</th>
                        <th>Return Stock</th>
                        <th>In Housekeeping</th>
                        <th>Unit</th>
                        <th>Min. Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="{{ $item->stock_quantity <= $item->min_stock_level ? 'table-warning' : '' }}">
                            <td><strong>{{ $item->name }}</strong></td>
                            <td>{{ Str::limit($item->description, 50) }}</td>
                            <td>
                                <span class="badge {{ $item->stock_quantity <= $item->min_stock_level ? 'bg-danger' : 'bg-success' }}">
                                    {{ $item->stock_quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $item->dirty_quantity > 0 ? 'bg-warning' : 'bg-light text-dark' }}">
                                    {{ $item->dirty_quantity }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $item->in_housekeeping_quantity }}
                                </span>
                            </td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->min_stock_level }}</td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    @can('process-laundry')
                                        @if($item->dirty_quantity > 0)
                                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#markCleanModal{{ $item->id }}" title="Mark as Clean">
                                                <i class="fas fa-soap"></i>
                                            </button>
                                        @endif
                                    @endcan
                                    @can('manage-laundry-items')
                                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#restockModal{{ $item->id }}" title="Restock">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                        <a href="{{ route('laundry-items.edit', $item) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('laundry-items.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>

                                {{-- Mark Clean Modal --}}
                                @if($item->dirty_quantity > 0)
                                <div class="modal fade" id="markCleanModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form action="{{ route('inventory-sync.mark-clean') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="laundry_item_id" value="{{ $item->id }}">
                                                <div class="modal-header">
                                                    <h6 class="modal-title">Wash Complete: {{ $item->name }}</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small">Quantity Washed</label>
                                                        <input type="number" name="quantity" class="form-control form-control-sm" value="{{ $item->dirty_quantity }}" max="{{ $item->dirty_quantity }}" min="1" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-sm btn-success">Mark Clean</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Restock Modal --}}
                                <div class="modal fade" id="restockModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form action="{{ route('inventory-sync.restock') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="laundry_item_id" value="{{ $item->id }}">
                                                <div class="modal-header">
                                                    <h6 class="modal-title">Restock: {{ $item->name }}</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="small text-muted mb-2">Current available stock: <strong>{{ $item->stock_quantity }}</strong></p>
                                                    <div class="mb-3">
                                                        <label class="form-label small">Quantity to Add</label>
                                                        <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="1" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small">Notes (optional)</label>
                                                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. New purchase">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-sm btn-info">Add Stock</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No inventory items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- Pending Requests from Housekeeping --}}
<div class="card mt-3">
    <div class="card-header bg-warning bg-opacity-25">
        <h5 class="mb-0"><i class="fas fa-bell me-2"></i> Pending Requests from Housekeeping ({{ isset($pendingRequests) ? $pendingRequests->count() : 0 }})</h5>
    </div>
    <div class="card-body p-0">
        @if(isset($pendingRequests) && $pendingRequests->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Requested By</th>
                        <th>Date</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRequests as $req)
                    <tr>
                        <td>
                            @if($req->transaction_type === 'clean_delivery')
                                <span class="badge bg-success">Request</span>
                            @else
                                <span class="badge bg-warning text-dark">Return</span>
                            @endif
                        </td>
                        <td><strong>{{ $req->item->name ?? 'N/A' }}</strong></td>
                        <td><span class="badge bg-primary">{{ $req->quantity }}</span></td>
                        <td>{{ $req->creator->name ?? 'Unknown' }}</td>
                        <td class="small">{{ $req->created_at->format('M d, H:i') }}</td>
                        <td class="small text-muted">{{ $req->notes ?? '-' }}</td>
                        <td>
                            <div class="btn-group">
                                <form action="{{ route('inventory-sync.approve-request', $req) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request for {{ $req->quantity }} {{ $req->item->name ?? 'items' }}?')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#denyModal{{ $req->id }}">
                                    <i class="fas fa-times"></i> Deny
                                </button>
                            </div>

                            {{-- Deny Modal --}}
                            <div class="modal fade" id="denyModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <form action="{{ route('inventory-sync.deny-request', $req) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h6 class="modal-title">Deny Request</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="small">Deny request for <strong>{{ $req->quantity }}</strong> x {{ $req->item->name ?? 'items' }}?</p>
                                                <div class="mb-3">
                                                    <label class="form-label small">Reason (optional)</label>
                                                    <input type="text" name="reason" class="form-control form-control-sm" placeholder="e.g. Out of stock">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-sm btn-danger">Deny Request</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center text-muted py-4">
            <i class="fas fa-inbox fa-2x mb-2"></i>
            <p class="mb-0">No pending requests. Requests from Housekeeping will appear here for approval.</p>
        </div>
        @endif
    </div>
</div>
@endsection

