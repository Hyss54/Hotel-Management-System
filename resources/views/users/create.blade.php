@extends('layouts.app')

@section('title', 'Add User (Staff)')
@section('page_title', 'Add New Staff Member')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Department *</label>
                        <select name="department_id" class="form-select" required>
                            <option value="">Select Department...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Assign Role *</label>
                        <select name="role" class="form-select" required>
                            <option value="">Select Role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', ucfirst($role->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3"></div> <!-- Spacer -->

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password *</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" required minlength="6">
                            <button class="btn btn-outline-secondary" type="button" tabindex="-1"
                                onclick="let input = this.previousElementSibling; if (input.type === 'password') { input.type = 'text'; this.innerHTML = '<i class=\'fas fa-eye-slash\'></i>'; } else { input.type = 'password'; this.innerHTML = '<i class=\'fas fa-eye\'></i>'; }">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Confirm Password *</label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" tabindex="-1"
                                onclick="let input = this.previousElementSibling; if (input.type === 'password') { input.type = 'text'; this.innerHTML = '<i class=\'fas fa-eye-slash\'></i>'; } else { input.type = 'password'; this.innerHTML = '<i class=\'fas fa-eye\'></i>'; }">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save User</button>
                        <a href="{{ route('users.index') }}" class="btn btn-light ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection