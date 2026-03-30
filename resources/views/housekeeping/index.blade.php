@extends('layouts.app')

@section('title', 'Housekeeping')
@section('page_title', 'Housekeeping Management')

@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Housekeeping Tasks</h5>
            @can('manage-housekeeping-tasks')
            <a href="{{ route('housekeeping.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Schedule Cleaning
            </a>
            @endcan
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('housekeeping.index') }}" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="room_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Rooms</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ request('room_id') == $room->id ? 'selected' : '' }}>
                                Room {{ $room->room_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Room</th>
                            <th>Task Type</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Scheduled</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td><strong>Room {{ $task->room->room_number }}</strong></td>
                                <td>{{ $task->task_type }}</td>
                                <td>{{ $task->assignee ? $task->assignee->name : 'Unassigned' }}</td>
                                <td>
                                    @can('update-housekeeping-status')
                                    <form action="{{ route('housekeeping.updateStatus', $task) }}" method="POST"
                                        class="d-inline-block">
                                        @csrf @method('PATCH')
                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto"
                                            onchange="this.form.submit()">
                                            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending
                                            </option>
                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In
                                                Progress</option>
                                            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>
                                                Completed</option>
                                        </select>
                                    </form>
                                    @else
                                        <span class="badge {{ $task->status == 'pending' ? 'bg-warning' : ($task->status == 'in_progress' ? 'bg-primary' : 'bg-success') }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                                    @endcan
                                </td>
                                <td>{{ $task->created_at->format('M d, H:i') }}</td>
                                <td class="text-end">
                                    @can('manage-housekeeping-tasks')
                                    <a href="{{ route('housekeeping.edit', $task) }}" class="btn btn-sm btn-outline-primary"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('housekeeping.destroy', $task) }}" method="POST"
                                        class="d-inline-block"
                                        onsubmit="return confirm('Are you sure you want to delete this task?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No housekeeping tasks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Linen & Laundry Management</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 border-end">
                    <h6 class="mb-3 text-muted uppercase small fw-bold">Current Inventory (In Housekeeping)</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>In Use</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($laundryItems as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $item->in_housekeeping_quantity }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-xs btn-outline-warning" data-bs-toggle="modal" data-bs-target="#sendDirtyModal{{ $item->id }}" title="Return to Laundry">
                                                <i class="fas fa-tshirt"></i> Return
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-success" data-bs-toggle="modal" data-bs-target="#requestCleanModal{{ $item->id }}" title="Request">
                                                <i class="fas fa-truck-loading"></i> Request
                                            </button>
                                        </div>

                                        {{-- Send Dirty Modal --}}
                                        <div class="modal fade" id="sendDirtyModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content">
                                                    <form action="{{ route('inventory-sync.collect-dirty') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="laundry_item_id" value="{{ $item->id }}">
                                                        <div class="modal-header text-dark">
                                                            <h6 class="modal-title">Return: {{ $item->name }}</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-dark">
                                                            <div class="mb-3 text-start">
                                                                <label class="form-label small">Quantity to Send</label>
                                                                <input type="number" name="quantity" class="form-control form-control-sm" value="{{ min(1, $item->in_housekeeping_quantity) }}" max="{{ $item->in_housekeeping_quantity }}" min="1" required>
                                                                <div class="form-text x-small">Available in Housekeeping: {{ $item->in_housekeeping_quantity }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-sm btn-warning">Send to Laundry</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Request Clean Modal --}}
                                        <div class="modal fade" id="requestCleanModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content">
                                                    <form action="{{ route('inventory-sync.request-clean') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="laundry_item_id" value="{{ $item->id }}">
                                                        <div class="modal-header text-dark">
                                                            <h6 class="modal-title">Request: {{ $item->name }}</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-dark">
                                                            <div class="mb-3 text-start">
                                                                <label class="form-label small">Quantity Requested</label>
                                                                <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="1" required>
                                                                <div class="form-text x-small">Available in Laundry: {{ $item->stock_quantity }}</div>
                                                            </div>
                                                            <div class="mb-3 text-start">
                                                                <label class="form-label small">Notes (optional)</label>
                                                                <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. Urgent for Room 305">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-sm btn-success">Submit Request</button>
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
                </div>
                <div class="col-md-6">
                    <h6 class="mb-3 text-muted uppercase small fw-bold">My Requests</h6>
                    @if(isset($pendingRequests) && $pendingRequests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $req)
                                    <tr>
                                        <td>{{ $req->item->name ?? 'N/A' }}</td>
                                        <td>{{ $req->quantity }}</td>
                                        <td>
                                            @if($req->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($req->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($req->status === 'denied')
                                                <span class="badge bg-danger">Denied</span>
                                            @endif
                                        </td>
                                        <td class="small">{{ $req->created_at->format('M d, H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small">No recent requests.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection