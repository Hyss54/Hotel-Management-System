@extends('layouts.app')

@section('title', 'Edit User')
@section('page_title', 'Edit Staff Member: ' . $user->name)

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department *</label>
                        <select name="department_id" class="form-select" required>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Assign Role *</label>
                        <select name="role" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($role->name)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-check-label mt-4">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            Account is Active
                        </label>
                    </div>

                    <div class="col-md-12 my-3">
                        <h5 class="text-danger border-bottom pb-2">Change Password (Leave blank to keep current)</h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">New Password</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" minlength="6">
                            <button class="btn btn-outline-secondary" type="button" tabindex="-1"
                                onclick="let input = this.previousElementSibling; if (input.type === 'password') { input.type = 'text'; this.innerHTML = '<i class=\'fas fa-eye-slash\'></i>'; } else { input.type = 'password'; this.innerHTML = '<i class=\'fas fa-eye\'></i>'; }">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1">Leave blank to keep current password</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" class="form-control">
                            <button class="btn btn-outline-secondary" type="button" tabindex="-1"
                                onclick="let input = this.previousElementSibling; if (input.type === 'password') { input.type = 'text'; this.innerHTML = '<i class=\'fas fa-eye-slash\'></i>'; } else { input.type = 'password'; this.innerHTML = '<i class=\'fas fa-eye\'></i>'; }">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update User</button>
                        <a href="{{ route('users.index') }}" class="btn btn-light ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection