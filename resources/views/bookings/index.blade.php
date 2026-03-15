@extends('layouts.app')

@section('title', 'Bookings')
@section('page_title', 'Bookings & Checks-ins')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Recent Bookings</span>
            <a href="{{ route('bookings.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> New Booking / Check-in
            </a>
        </div>

        <div class="card-body border-bottom bg-light">
            <form method="GET" class="row gx-2 gy-2 align-items-center">
                <div class="col-auto">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Booking No or Guest Name" value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In
                        </option>
                        <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out
                        </option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Booking No.</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Check-in/Out</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td><strong>{{ $booking->booking_number }}</strong></td>
                                <td>{{ $booking->customer->name }}</td>
                                <td>
                                    Room {{ $booking->room->room_number }}
                                    <br><small class="text-muted">{{ $booking->room->roomType->name }}</small>
                                </td>
                                <td>
                                    <small>
                                        <strong>In:</strong> {{ $booking->check_in_date->format('M d, Y') }}<br>
                                        <strong>Out:</strong> {{ $booking->check_out_date->format('M d, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $booking->status == 'checked_in' ? 'success' : ($booking->status == 'checked_out' ? 'secondary' : 'primary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-sm btn-info text-white">View
                                        Manage</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No bookings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $bookings->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection