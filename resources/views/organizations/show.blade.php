@extends('layouts.app')

@section('title', 'Organization Details')
@section('page_title', 'Organization: ' . $organization->name)

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Overview</h5>
                    <a href="{{ route('organizations.edit', $organization) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold">Registration Number</label>
                        <div>{{ $organization->registration_number ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold">Phone</label>
                        <div>{{ $organization->phone }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold">Email</label>
                        <div>{{ $organization->email ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold">Status</label>
                        <div>
                            @if($organization->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small text-uppercase fw-bold">Address</label>
                        <div class="text-wrap">{{ $organization->address ?? 'Not provided' }}</div>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small text-uppercase fw-bold">Notes</label>
                        <div class="text-wrap small text-muted">{{ $organization->notes ?? 'No notes' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Associated Guests / Customers</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('organizations.receipt', $organization) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-invoice"></i> Aggregated Receipt
                        </a>
                        <a href="{{ route('customers.create', ['organization_id' => $organization->id]) }}"
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-user-plus"></i> Add Guest
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Guest Name</th>
                                    <th>Contact</th>
                                    <th>ID Details</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($organization->customers as $customer)
                                    <tr>
                                        <td>
                                            <strong>{{ $customer->name }}</strong>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fas fa-phone fa-xs text-muted"></i> {{ $customer->phone }}<br>
                                                <i class="fas fa-envelope fa-xs text-muted"></i> {{ $customer->email ?? 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <strong>{{ strtoupper($customer->id_type ?? 'N/A') }}:</strong>
                                                {{ $customer->id_number ?? 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('customers.edit', $customer) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No guests associated with this
                                            organization yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection