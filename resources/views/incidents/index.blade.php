@extends('layouts.app')

@section('title', 'Incident Reports')
@section('page_title', 'All Incident Reports')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Reports List</h5>
            @can('manage-incidents')
            <a href="{{ route('incidents.create') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-plus-circle"></i> Log New Incident
            </a>
            @endcan
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Incident</th>
                            <th>Guest / Room</th>
                            <th>Status</th>
                            <th>Reporter</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidents as $incident)
                            <tr>
                                <td>
                                    <small class="text-muted d-block">{{ $incident->created_at->format('M d, Y') }}</small>
                                    <strong>{{ $incident->created_at->format('H:i') }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-circle me-2 text-{{ $incident->severity == 'emergency' ? 'danger' : ($incident->severity == 'high' ? 'warning' : ($incident->severity == 'medium' ? 'primary' : 'info')) }}"
                                            style="font-size: 0.6rem;"></i>
                                        <div>
                                            <strong>{{ $incident->title }}</strong><br>
                                            <small class="text-muted">{{ Str::limit($incident->description, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $incident->customer->name }} <br>
                                    @if($incident->room)
                                        <span class="badge bg-light text-dark">Room {{ $incident->room->room_number }}</span>
                                    @endif
                                </td>
                                <td>
                                    @can('manage-incidents')
                                    <form action="{{ route('incidents.updateStatus', $incident) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="status"
                                            class="form-select form-select-sm w-auto {{ $incident->status == 'open' ? 'text-danger fw-bold' : ($incident->status == 'resolved' ? 'text-success' : '') }}"
                                            onchange="this.form.submit()">
                                            <option value="open" {{ $incident->status == 'open' ? 'selected' : '' }}>Open</option>
                                            <option value="resolved" {{ $incident->status == 'resolved' ? 'selected' : '' }}>
                                                Resolved</option>
                                            <option value="escalated" {{ $incident->status == 'escalated' ? 'selected' : '' }}>
                                                Escalated</option>
                                        </select>
                                    </form>
                                    @else
                                        <span class="badge {{ $incident->status == 'open' ? 'bg-danger' : ($incident->status == 'resolved' ? 'bg-success' : 'bg-warning') }}">{{ ucfirst($incident->status) }}</span>
                                    @endcan
                                </td>
                                <td><small>{{ $incident->reporter->name }}</small></td>
                                <td class="text-end">
                                    <a href="{{ route('incidents.show', $incident) }}" class="btn btn-sm btn-outline-secondary"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No incident reports found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $incidents->links() }}
        </div>
    </div>
@endsection