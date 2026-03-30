<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\HousekeepingTask;
use App\Models\InventoryTransaction;
use App\Models\LaundryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class HousekeepingTaskController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-housekeeping-tasks');
        $query = HousekeepingTask::with(['room', 'assignee', 'creator'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->room_id) {
            $query->where('room_id', $request->room_id);
        }

        $tasks = $query->paginate(20)->withQueryString();
        $rooms = Room::all();
        $laundryItems = LaundryItem::where('is_active', true)->get();
        $pendingRequests = InventoryTransaction::with('item')
            ->where('transaction_type', 'clean_delivery')
            ->latest()
            ->take(20)
            ->get();

        return view('housekeeping.index', compact('tasks', 'rooms', 'laundryItems', 'pendingRequests'));
    }

    public function create()
    {
        Gate::authorize('manage-housekeeping-tasks');
        $rooms = Room::where('is_active', true)->get();
        $staff = User::whereHas('department', function ($q) {
            $q->where('name', 'Housekeeping');
        })->get();

        // If no housekeeping staff, just get all users for now to avoid blocking
        if ($staff->isEmpty()) {
            $staff = User::all();
        }

        return view('housekeeping.create', compact('rooms', 'staff'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-housekeeping-tasks');
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'task_type' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        $data['status'] = 'pending';

        HousekeepingTask::create($data);

        return redirect()->route('housekeeping.index')->with('success', 'Housekeeping task scheduled.');
    }

    public function edit(HousekeepingTask $housekeeping)
    {
        Gate::authorize('manage-housekeeping-tasks');
        $rooms = Room::where('is_active', true)->get();
        $staff = User::all(); // Simplified for now

        return view('housekeeping.edit', compact('housekeeping', 'rooms', 'staff'));
    }

    public function update(Request $request, HousekeepingTask $housekeeping)
    {
        Gate::authorize('manage-housekeeping-tasks');
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'task_type' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'notes' => 'nullable|string',
        ]);

        $housekeeping->update($data);

        // Sync room status
        if ($housekeeping->status == 'in_progress') {
            $housekeeping->room->update(['status' => 'cleaning']);
        } elseif ($housekeeping->status == 'completed') {
            $housekeeping->room->update(['status' => 'available']);
        }

        return redirect()->route('housekeeping.index')->with('success', 'Housekeeping task updated.');
    }

    public function destroy(HousekeepingTask $housekeeping)
    {
        Gate::authorize('manage-housekeeping-tasks');
        $housekeeping->delete();
        return redirect()->route('housekeeping.index')->with('success', 'Housekeeping task deleted.');
    }

    public function updateStatus(Request $request, HousekeepingTask $housekeeping)
    {
        Gate::authorize('update-housekeeping-status');
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $housekeeping->update(['status' => $request->status]);

        // Sync room status
        if ($request->status == 'in_progress') {
            $housekeeping->room->update(['status' => 'cleaning']);
        } elseif ($request->status == 'completed') {
            $housekeeping->room->update(['status' => 'available']);
        }

        return back()->with('success', 'Task status updated.');
    }
}
