<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::withCount('rooms')->paginate(15);
        return view('room-types.index', compact('roomTypes'));
    }

    public function create()
    {
        return view('room-types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:room_types,name',
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'amenities' => 'nullable|string',
        ]);
        $data['amenities'] = $request->amenities ? array_filter(array_map('trim', explode(',', $request->amenities))) : [];
        RoomType::create($data);
        return redirect()->route('room-types.index')->with('success', 'Room type created.');
    }

    public function edit(RoomType $roomType)
    {
        return view('room-types.edit', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType)
    {
        $data = $request->validate([
            'name' => 'required|unique:room_types,name,' . $roomType->id,
            'description' => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'max_guests' => 'required|integer|min:1',
            'amenities' => 'nullable|string',
        ]);
        $data['amenities'] = $request->amenities ? array_filter(array_map('trim', explode(',', $request->amenities))) : [];
        $roomType->update($data);
        return redirect()->route('room-types.index')->with('success', 'Room type updated.');
    }

    public function destroy(RoomType $roomType)
    {
        if ($roomType->rooms()->exists()) {
            return back()->with('error', 'Cannot delete a room type that has rooms assigned.');
        }
        $roomType->delete();
        return redirect()->route('room-types.index')->with('success', 'Room type deleted.');
    }
}
