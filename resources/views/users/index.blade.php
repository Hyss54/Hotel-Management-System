@extends('layouts.app')

@section('title', 'Users & Staff')
@section('page_title', 'User Management')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Staff Directory</span>
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add User
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}<br><small class="text-muted">{{ $user->phone }}</small></td>
                                <td>
                                    @if($user->department)
                                        <span class="badge bg-secondary">{{ $user->department->name }}</span>
                                    @else
                                        <span class="text-muted">None</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-info">{{ str_replace('_', ' ', ucfirst($role->name)) }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-info me-1" title="View Details"
                                            data-bs-toggle="modal" data-bs-target="#viewUserModal{{ $user->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-secondary me-1"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-danger disabled"
                                                title="Cannot delete your own account">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- View User Modal -->
                                    <div class="modal fade" id="viewUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Staff Details: {{ $user->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="text-center mb-4">
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff&size=100"
                                                            class="rounded-circle shadow-sm">
                                                    </div>
                                                    <table class="table table-borderless">
                                                        <tr>
                                                            <th width="35%" class="text-muted">Full Name</th>
                                                            <td>{{ $user->name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted">Email</th>
                                                            <td>{{ $user->email }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted">Phone</th>
                                                            <td>{{ $user->phone ?: 'Not provided' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted">Department</th>
                                                            <td>
                                                                @if($user->department)
                                                                    <span
                                                                        class="badge bg-secondary">{{ $user->department->name }}</span>
                                                                @else
                                                                    <span class="text-muted">None</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted">Roles</th>
                                                            <td>
                                                                @foreach($user->roles as $role)
                                                                    <span
                                                                        class="badge bg-info">{{ str_replace('_', ' ', ucfirst($role->name)) }}</span>
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted">Status</th>
                                                            <td>
                                                                @if($user->is_active)
                                                                    <span class="badge bg-success">Active</span>
                                                                @else
                                                                    <span class="badge bg-danger">Inactive</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-muted">Registered On</th>
                                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit
                                                        User</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection