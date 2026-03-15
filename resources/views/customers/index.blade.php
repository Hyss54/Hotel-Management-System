@extends('layouts.app')

@section('title', 'Customers')
@section('page_title', 'Customers Management')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="true" href="{{ route('customers.index') }}">Guests /
                        Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('organizations.index') }}">Organizations</a>
                </li>
            </ul>
            <a href="{{ route('customers.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Customer
            </a>
        </div>

        <div class="card-body border-bottom bg-light">
            <form method="GET" class="row gx-2 gy-2 align-items-center">
                <div class="col-auto">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Search name, phone, email..." value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="individual" {{ request('type') == 'individual' ? 'selected' : '' }}>Individual</option>
                        <option value="organization" {{ request('type') == 'organization' ? 'selected' : '' }}>
                            Organization Guest</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Type / Organization</th>
                            <th>ID Details</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            <tr>
                                <td>
                                    <strong>{{ $customer->name }}</strong>
                                    @if(!$customer->is_active)
                                        <span class="badge bg-danger ms-1">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-phone fa-sm text-muted"></i> {{ $customer->phone }}<br>
                                    <i class="fas fa-envelope fa-sm text-muted"></i> {{ $customer->email ?? 'N/A' }}
                                </td>
                                <td>
                                    @if($customer->type == 'organization' && $customer->organization)
                                        <span class="badge bg-info"><i class="fas fa-building"></i>
                                            {{ $customer->organization->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Individual</span>
                                    @endif
                                </td>
                                <td>
                                    <small>
                                        <strong>{{ strtoupper($customer->id_type ?? 'N/A') }}:</strong>
                                        {{ $customer->id_number ?? 'N/A' }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('customers.edit', $customer) }}"
                                        class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i> Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection