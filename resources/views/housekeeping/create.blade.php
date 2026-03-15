@extends('layouts.app')

@section('title', 'Schedule Housekeeping')
@section('page_title', 'Schedule New Task')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('housekeeping.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Room *</label>
                        <select name="room_id" class="form-select" required>
                            <option value="">Select Room...</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    Room {{ $room->room_number }} ({{ $room->roomType->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Task Type *</label>
                        <select name="task_type" class="form-select" required>
                            <option value="Regular Cleaning" {{ old('task_type') == 'Regular Cleaning' ? 'selected' : '' }}>
                                Regular Cleaning</option>
                            <option value="Deep Cleaning" {{ old('task_type') == 'Deep Cleaning' ? 'selected' : '' }}>Deep
                                Cleaning</option>
                            <option value="Maintenance" {{ old('task_type') == 'Maintenance' ? 'selected' : '' }}>Maintenance
                                / Repair</option>
                            <option value="Change Linens" {{ old('task_type') == 'Change Linens' ? 'selected' : '' }}>Change
                                Linens</option>
                            <option value="Turn Down Service" {{ old('task_type') == 'Turn Down Service' ? 'selected' : '' }}>
                                Turn Down Service</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Assigned To (Staff)</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">Unassigned</option>
                            @foreach($staff as $person)
                                <option value="{{ $person->id }}" {{ old('assigned_to') == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }} {{ $person->department ? '(' . $person->department->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Notes / Special Instructions</label>
                    <textarea name="notes" class="form-control" rows="3"
                        placeholder="e.g., Guest requested extra towels, Fix leaking tap...">{{ old('notes') }}</textarea>
                </div>

                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary">Schedule Task</button>
                    <a href="{{ route('housekeeping.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection