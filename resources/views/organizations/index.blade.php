@extends('layouts.app')

@section('title', 'Organizations')
@section('page_title', 'Organizations Management')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('customers.index') }}">Guests / Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="true"
                        href="{{ route('organizations.index') }}">Organizations</a>
                </li>
            </ul>
            <a href="{{ route('organizations.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Organization
            </a>
        </div>

        <div class="card-body border-bottom bg-light">
            <form method="GET" class="row gx-2 gy-2 align-items-center">
                <div class="col-auto">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search name, phone, email..." value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
                    <a href="{{ route('organizations.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Organization Name</th>
                            <th>Contact Info</th>
                            <th>Reg. Number</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organizations as $org)
                            <tr>
                                <td>
                                    <strong>{{ $org->name }}</strong>
                                </td>
                                <td>
                                    <i class="fas fa-phone fa-sm text-muted"></i> {{ $org->phone }}<br>
                                    <i class="fas fa-envelope fa-sm text-muted"></i> {{ $org->email ?? 'N/A' }}
                                </td>
                                <td>{{ $org->registration_number ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $org->customers_count }} Guests
                                    </span>
                                </td>
                                <td>
                                    @if($org->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('organizations.show', $org) }}" class="btn btn-sm btn-outline-info"
                                            title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('organizations.edit', $org) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No organizations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $organizations->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection