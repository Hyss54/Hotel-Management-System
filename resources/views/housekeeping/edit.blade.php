@extends('layouts.app')

@section('title', 'Edit Housekeeping Task')
@section('page_title', 'Edit Task: Room ' . $housekeeping->room->room_number)

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('housekeeping.update', $housekeeping) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Room *</label>
                        <select name="room_id" class="form-select" required>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id', $housekeeping->room_id) == $room->id ? 'selected' : '' }}>
                                    Room {{ $room->room_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Task Type *</label>
                        <input type="text" name="task_type" class="form-control"
                            value="{{ old('task_type', $housekeeping->task_type) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ old('status', $housekeeping->status) == 'pending' ? 'selected' : '' }}>
                                Pending</option>
                            <option value="in_progress" {{ old('status', $housekeeping->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $housekeeping->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">Unassigned</option>
                            @foreach($staff as $person)
                                <option value="{{ $person->id }}" {{ old('assigned_to', $housekeeping->assigned_to) == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $housekeeping->notes) }}</textarea>
                </div>

                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary">Update Task</button>
                    <a href="{{ route('housekeeping.index') }}" class="btn btn-light ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection