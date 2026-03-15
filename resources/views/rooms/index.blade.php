@extends('layouts.app')

@section('title', 'Rooms')
@section('page_title', 'Rooms Management')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>All Rooms</span>
            <a href="{{ route('rooms.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Room
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Room No.</th>
                            <th>Type</th>
                            <th>Floor</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rooms as $room)
                            <tr>
                                <td><strong>{{ $room->room_number }}</strong></td>
                                <td>{{ $room->roomType->name }}<br><small
                                        class="text-muted">GH₵{{ $room->roomType->price_per_night }}/night</small></td>
                                <td>Floor {{ $room->floor }}</td>
                                <td>
                                    <span class="badge bg-{{ $room->status }}">{{ ucfirst($room->status) }}</span>
                                    @if(!$room->is_active)
                                        <span class="badge bg-dark">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('rooms.updateStatus', $room) }}" method="POST"
                                        class="d-inline-block me-1">
                                        @csrf @method('PATCH')
                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto"
                                            onchange="this.form.submit()">
                                            <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>
                                                Available</option>
                                            <option value="occupied" {{ $room->status == 'occupied' ? 'selected' : '' }} disabled>
                                                Occupied</option>
                                            <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }}>
                                                Maintenance</option>
                                            <option value="reserved" {{ $room->status == 'reserved' ? 'selected' : '' }}>Reserved
                                            </option>
                                        </select>
                                    </form>
                                    <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-outline-secondary"><i
                                            class="fas fa-edit"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No rooms found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $rooms->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection