@extends('layouts.app')

@section('title', 'Add Customer')
@section('page_title', 'Add New Customer')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="text-danger border-bottom pb-2">In Case of Emergency (ICE)</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="form-control"
                            value="{{ old('emergency_contact_name') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Emergency Contact Phone</label>
                        <input type="text" name="emergency_contact_phone" class="form-control"
                            value="{{ old('emergency_contact_phone') }}">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="text-primary border-bottom pb-2">Basic Information</h5>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone Number *</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone') }}" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                            value="{{ old('address') }}">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="text-primary border-bottom pb-2">Identification</h5>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Type</label>
                        <select name="id_type" class="form-select @error('id_type') is-invalid @enderror">
                            <option value="">Select ID Type...</option>
                            <option value="passport" {{ old('id_type') == 'passport' ? 'selected' : '' }}>Passport</option>
                            <option value="national_id" {{ old('id_type') == 'national_id' ? 'selected' : '' }}>National ID
                            </option>
                            <option value="driver_license" {{ old('id_type') == 'driver_license' ? 'selected' : '' }}>Driver's
                                License</option>
                            <option value="other" {{ old('id_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('id_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Number</label>
                        <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror"
                            value="{{ old('id_number') }}">
                        @error('id_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="organization" {{ old('type') == 'organization' ? 'selected' : '' }}>
                                Organization Guest</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="text-primary border-bottom pb-2">Organization Details</h5>
                    </div>
                    <div class="col-md-6 mb-3" id="organizationField"
                        style="display: {{ old('type') == 'organization' ? 'block' : 'none' }};">
                        <label class="form-label">Select Organization</label>
                        <select name="organization_id" id="organization_id"
                            class="form-select @error('organization_id') is-invalid @enderror">
                            <option value="">-- Select Organization --</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}" {{ old('organization_id', $selectedOrganizationId ?? '') == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('organization_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted"><a href="{{ route('organizations.create') }}">Add new organization</a> if
                            not
                            listed.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Customer</button>
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

            document.addEventListener('DOMContentLoaded', function () {
                // Initial check on load to ensure the organization field visibility is correct based on old('type')
                toggleOrganizationField();

                // If an organization ID is pre-filled (e.g., from a query parameter or old input),
                // ensure the customer type is set to 'organization' and the field is shown.
                var selectedOrgId = '{{ old("organization_id", $selectedOrganizationId ?? "") }}';
                if (selectedOrgId) {
                    document.getElementById('customerType').value = 'organization';
                    toggleOrganizationField(); // Call again to ensure visibility
                }
            });
        </script>
    @endpush
@endsection
```