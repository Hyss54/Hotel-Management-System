@extends('layouts.app')

@section('title', 'Edit Customer')
@section('page_title', 'Edit Customer: ' . $customer->name)

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('customers.update', $customer) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="text-danger border-bottom pb-2">In Case of Emergency (ICE)</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="form-control"
                            value="{{ old('emergency_contact_name', $customer->emergency_contact_name) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Emergency Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" class="form-control"
                            value="{{ old('emergency_contact_phone', $customer->emergency_contact_phone) }}">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="text-primary border-bottom pb-2">Basic Information</h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $customer->name) }}"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number *</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $customer->phone) }}"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control"
                            value="{{ old('address', $customer->address) }}">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="text-primary border-bottom pb-2">Customer Type</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Type *</label>
                        <select name="type" id="customerType" class="form-select @error('type') is-invalid @enderror"
                            required onchange="toggleOrganizationField()">
                            <option value="individual" {{ old('type', $customer->type) == 'individual' ? 'selected' : '' }}>
                                Individual</option>
                            <option value="organization" {{ old('type', $customer->type) == 'organization' ? 'selected' : '' }}>Organization Guest</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3" id="organizationField"
                        style="display: {{ old('type', $customer->type) == 'organization' ? 'block' : 'none' }};">
                        <label class="form-label">Select Organization</label>
                        <select name="organization_id" class="form-select">
                            <option value="">Choose organization...</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}" {{ old('organization_id', $customer->organization_id) == $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-light ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleOrganizationField() {
                const typeSelect = document.getElementById('customerType');
                const orgField = document.getElementById('organizationField');
                if (typeSelect.value === 'organization') {
                    orgField.style.display = 'block';
                } else {
                    orgField.style.display = 'none';
                }
            }
        </script>
    @endpush
@endsection