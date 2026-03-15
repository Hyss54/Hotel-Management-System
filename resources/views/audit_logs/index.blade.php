@extends('layouts.app')

@section('title', 'Audit Trail')
@section('page_title', 'System Audit Trail')

@section('content')
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <form action="{{ route('audit-logs.index') }}" method="GET" class="row g-2 flex-grow-1 me-3">
                    <div class="col-md-3">
                        <select name="user_id" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="event" class="form-select form-select-sm">
                            <option value="">All Events</option>
                            <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="auditable_type" class="form-control form-select-sm"
                            placeholder="Model (e.g. Room)" value="{{ request('auditable_type') }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-light btn-sm flex-fill">Clear Filters</a>
                    </div>
                </form>
                @if(auth()->check() && auth()->user()->hasRole('admin'))
                    <form action="{{ route('audit-logs.clear') }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to clear ALL audit logs? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm text-nowrap">
                            <i class="fas fa-trash-alt me-1"></i> Clear Logs
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Event</th>
                            <th>Module</th>
                            <th>Record ID</th>
                            <th>Changes</th>
                            <th class="text-end">Context</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <small class="text-muted d-block">{{ $log->created_at->format('M d, Y') }}</small>
                                    <strong>{{ $log->created_at->format('H:i:s') }}</strong>
                                </td>
                                <td>
                                    {{ $log->user->name ?? 'System' }}
                                    <br><small class="text-muted">{{ $log->ip_address }}</small>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $log->event == 'created' ? 'success' : ($log->event == 'updated' ? 'primary' : 'danger') }}">
                                        {{ strtoupper($log->event) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="fw-bold">{{ class_basename($log->auditable_type) }}</small>
                                </td>
                                <td>#{{ $log->auditable_id }}</td>
                                <td>
                                    @if($log->event == 'updated')
                                        <button class="btn btn-sm btn-outline-info p-1" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#log-{{ $log->id }}">
                                            View Changes
                                        </button>
                                        <div class="collapse mt-2" id="log-{{ $log->id }}">
                                            <div class="card card-body p-2 bg-light small">
                                                @foreach($log->new_values as $key => $value)
                                                    @if($key !== 'updated_at')
                                                        <div>
                                                            <strong>{{ $key }}:</strong>
                                                            <span
                                                                class="text-danger"><del>{{ is_array($log->old_values[$key] ?? '') ? json_encode($log->old_values[$key]) : ($log->old_values[$key] ?? 'N/A') }}</del></span>
                                                            <i class="fas fa-arrow-right mx-1"></i>
                                                            <span
                                                                class="text-success">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @elseif($log->event == 'created')
                                        <span class="text-muted small">New record created</span>
                                    @else
                                        <span class="text-muted small">Record deleted</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <i class="fas fa-info-circle text-muted" title="{{ $log->user_agent }}"></i>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No audit logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $logs->links() }}
        </div>
    </div>
@endsection