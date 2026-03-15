<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Incident;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class IncidentController extends Controller
{
    public function index()
    {
        Gate::authorize('view-incidents');
        $incidents = Incident::with(['customer', 'room', 'reporter', 'booking'])->latest()->paginate(15);
        return view('incidents.index', compact('incidents'));
    }

    public function create(Request $request)
    {
        Gate::authorize('manage-incidents');
        $customers = Customer::where('is_active', true)->get();
        $rooms = Room::where('is_active', true)->get();

        $selected_customer = $request->customer_id ? Customer::find($request->customer_id) : null;
        $selected_room = $request->room_id ? Room::find($request->room_id) : null;
        $selected_booking = $request->booking_id ? Booking::find($request->booking_id) : null;

        return view('incidents.create', compact('customers', 'rooms', 'selected_customer', 'selected_room', 'selected_booking'));
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-incidents');
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'room_id' => 'nullable|exists:rooms,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,emergency',
        ]);

        $data['reported_by'] = auth()->id();
        $data['status'] = 'open';

        Incident::create($data);

        return redirect()->route('incidents.index')->with('success', 'Incident report logged successfully.');
    }

    public function show(Incident $incident)
    {
        Gate::authorize('view-incidents');
        $incident->load(['customer', 'room', 'reporter', 'booking', 'auditLogs.user']);
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        Gate::authorize('manage-incidents');
        $customers = Customer::where('is_active', true)->get();
        $rooms = Room::where('is_active', true)->get();
        return view('incidents.edit', compact('incident', 'customers', 'rooms'));
    }

    public function update(Request $request, Incident $incident)
    {
        Gate::authorize('manage-incidents');
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'room_id' => 'nullable|exists:rooms,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high,emergency',
        ]);

        $incident->update($data);

        return redirect()->route('incidents.show', $incident)->with('success', 'Incident report updated.');
    }

    public function updateStatus(Request $request, Incident $incident)
    {
        Gate::authorize('manage-incidents');
        $request->validate([
            'status' => 'required|in:open,resolved,escalated',
        ]);

        $incident->update(['status' => $request->status]);

        return back()->with('success', 'Incident status updated.');
    }
}
