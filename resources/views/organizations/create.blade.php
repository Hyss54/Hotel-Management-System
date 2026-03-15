@extends('layouts.app')

@section('title', 'Add Organization')
@section('page_title', 'Register New Organization')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Organization Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('organizations.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Organization Name *</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Registration Number</label>
                                <input type="text" name="registration_number"
                                    class="form-control @error('registration_number') is-invalid @enderror"
                                    value="{{ old('registration_number') }}">
                                @error('registration_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number *</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                                    rows="2">{{ old('address') }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                    rows="3">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('organizations.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Register Organization
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection