<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('roomType')->orderBy('room_number')->paginate(20);
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        $roomTypes = RoomType::where('is_active', true)->get();
        return view('rooms.create', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1',
        ]);
        $data = $request->only(['room_number', 'room_type_id', 'floor', 'notes']);
        $data['is_active'] = $request->has('is_active');
        Room::create($data);
        return redirect()->route('rooms.index')->with('success', 'Room created successfully.');
    }

    public function show(Room $room)
    {
        $room->load(['roomType', 'bookings.customer']);
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $roomTypes = RoomType::where('is_active', true)->get();
        return view('rooms.edit', compact('room', 'roomTypes'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1',
        ]);
        $data = $request->only(['room_number', 'room_type_id', 'floor', 'status', 'notes']);
        $data['is_active'] = $request->has('is_active');
        $room->update($data);
        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        if ($room->bookings()->where('status', 'checked_in')->exists()) {
            return back()->with('error', 'Cannot delete a room with active check-ins.');
        }
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Room deleted.');
    }

    public function updateStatus(Request $request, Room $room)
    {
        $request->validate(['status' => 'required|in:available,occupied,maintenance,reserved']);
        $room->update(['status' => $request->status]);
        return back()->with('success', 'Room status updated.');
    }
}
