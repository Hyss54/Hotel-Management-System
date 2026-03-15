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
@endsection