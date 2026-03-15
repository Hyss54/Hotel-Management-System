@extends('layouts.app')

@section('title', 'Incident Details')
@section('page_title', 'Incident: ' . $incident->title)

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Report Details</h5>
                    <span
                        class="badge bg-{{ $incident->severity == 'emergency' ? 'danger' : ($incident->severity == 'high' ? 'warning' : ($incident->severity == 'medium' ? 'primary' : 'info')) }}">
                        {{ strtoupper($incident->severity) }} SEVERITY
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase">Title</h6>
                        <p class="fs-5 fw-bold mb-0">{{ $incident->title }}</p>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase">Description</h6>
                        <div class="bg-light p-3 rounded border">
                            {!! nl2br(e($incident->description)) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted small text-uppercase">Reported By</h6>
                            <p class="mb-0">{{ $incident->reporter->name }}</p>
                            <small class="text-muted">{{ $incident->created_at->format('M d, Y @ H:i') }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted small text-uppercase">Current Status</h6>
                            @can('manage-incidents')
                            <form action="{{ route('incidents.updateStatus', $incident) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="input-group">
                                    <select name="status" class="form-select">
                                        <option value="open" {{ $incident->status == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="resolved" {{ $incident->status == 'resolved' ? 'selected' : '' }}>
                                            Resolved</option>
                                        <option value="escalated" {{ $incident->status == 'escalated' ? 'selected' : '' }}>
                                            Escalated</option>
                                    </select>
                                    <button type="submit" class="btn btn-dark">Update</button>
                                </div>
                            </form>
                            @else
                                <span class="badge {{ $incident->status == 'open' ? 'bg-danger' : ($incident->status == 'resolved' ? 'bg-success' : 'bg-warning') }}">{{ ucfirst($incident->status) }}</span>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Guest Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">Guest Information</div>
                <div class="card-body">
                    <h6 class="mb-1">{{ $incident->customer->name }}</h6>
                    <p class="mb-3 text-muted"><i class="fas fa-phone small me-1"></i> {{ $incident->customer->phone }}</p>

                    @if($incident->customer->emergency_contact_name)
                        <div class="alert alert-warning p-2 small mb-3">
                            <i class="fas fa-exclamation-circle me-1"></i> <strong>ICE:</strong>
                            {{ $incident->customer->emergency_contact_name }}<br>
                            <span class="ms-4">{{ $incident->customer->emergency_contact_phone }}</span>
                        </div>
                    @endif

                    @if($incident->room)
                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                            <span>Room Number</span>
                            <span class="badge bg-dark fw-bold">Room {{ $incident->room->room_number }}</span>
                        </div>
                    @endif

                    @if($incident->booking)
                        <div class="mt-3 text-center">
                            <a href="{{ route('bookings.show', $incident->booking) }}"
                                class="btn btn-sm btn-outline-primary w-100">
                                View Associated Booking
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection