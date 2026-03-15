@extends('layouts.app')

@section('title', 'Room Types')
@section('page_title', 'Room Types Configuration')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Room Types</h5>
            <a href="{{ route('room-types.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Room Type
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price per Night</th>
                            <th>Max Guests</th>
                            <th>Amenities</th>
                            <th>Rooms Assigned</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roomTypes as $type)
                            <tr>
                                <td><strong>{{ $type->name }}</strong></td>
                                <td>GH₵{{ number_format($type->price_per_night, 2) }}</td>
                                <td>{{ $type->max_guests }}</td>
                                <td>
                                    @if($type->amenities && count($type->amenities) > 0)
                                        @foreach(array_slice($type->amenities, 0, 3) as $amenity)
                                            <span class="badge bg-light text-dark border">{{ $amenity }}</span>
                                        @endforeach
                                        @if(count($type->amenities) > 3)
                                            <span class="badge bg-light text-dark border">+{{ count($type->amenities) - 3 }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted"><small>None</small></span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $type->rooms_count > 0 ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $type->rooms_count }} Rooms
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-info" title="View Details"
                                        data-bs-toggle="modal" data-bs-target="#viewModal{{ $type->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="{{ route('room-types.edit', $type) }}" class="btn btn-sm btn-outline-primary"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($type->rooms_count == 0)
                                        <form action="{{ route('room-types.destroy', $type) }}" method="POST" class="d-inline-block"
                                            onsubmit="return confirm('Are you sure you want to delete this room type?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-danger disabled"
                                            title="Cannot delete: Rooms are assigned">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif

                                    <!-- View Modal -->
                                    <div class="modal fade text-start" id="viewModal{{ $type->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $type->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-6 text-muted">Price per Night</div>
                                                        <div class="col-6 fw-bold">
                                                            GH₵{{ number_format($type->price_per_night, 2) }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-6 text-muted">Max Guests</div>
                                                        <div class="col-6 fw-bold">{{ $type->max_guests }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-6 text-muted">Rooms Assigned</div>
                                                        <div class="col-6 fw-bold">
                                                            <span
                                                                class="badge {{ $type->rooms_count > 0 ? 'bg-success' : 'bg-secondary' }}">
                                                                {{ $type->rooms_count }} Rooms
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-12 text-muted mb-1">Amenities</div>
                                                        <div class="col-12">
                                                            @if($type->amenities && count($type->amenities) > 0)
                                                                @foreach($type->amenities as $amenity)
                                                                    <span
                                                                        class="badge bg-light text-dark border me-1 mb-1">{{ $amenity }}</span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted fst-italic">No amenities listed</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 text-muted mb-1">Description</div>
                                                        <div class="col-12 bg-light p-3 rounded rounded-3">
                                                            {{ $type->description ?: 'No description provided.' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No room types configured yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $roomTypes->links() }}
            </div>
        </div>
    </div>
@endsection