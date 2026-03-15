<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::withCount('customers')->paginate(20);
        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ]);

        Organization::create($request->all());

        return redirect()->route('organizations.index')
            ->with('success', 'Organization registered successfully.');
    }

    public function show(Organization $organization)
    {
        $organization->load([
            'customers' => function ($query) {
                $query->latest();
            }
        ]);
        return view('organizations.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $organization->update($request->all());

        return redirect()->route('organizations.show', $organization)
            ->with('success', 'Organization updated successfully.');
    }

    public function receipt(Organization $organization)
    {
        $organization->load([
            'customers.bookings' => function ($query) {
                $query->whereIn('status', ['checked_out', 'checked_in'])
                    ->with(['room.roomType', 'bill.payments']);
            }
        ]);

        $bookings = collect();
        foreach ($organization->customers as $customer) {
            foreach ($customer->bookings as $booking) {
                if ($booking->bill) { // Only aggregate bookings that have a bill generated
                    $bookings->push($booking);
                }
            }
        }

        $totalRoomCharge = $bookings->sum(fn($b) => $b->bill->room_charge);
        $totalKitchenCharge = $bookings->sum(fn($b) => $b->bill->kitchen_charge);
        $totalLaundryCharge = $bookings->sum(fn($b) => $b->bill->laundry_charge);
        $totalBarCharge = $bookings->sum(fn($b) => $b->bill->bar_charge);
        $totalOtherCharges = $bookings->sum(fn($b) => $b->bill->other_charges);
        $totalDiscount = $bookings->sum(fn($b) => $b->bill->discount);

        $grandTotal = $bookings->sum(fn($b) => $b->bill->total_amount);
        $totalPaid = $bookings->sum(fn($b) => $b->bill->paid_amount);
        $balance = $bookings->sum(fn($b) => $b->bill->balance);

        return view('organizations.receipt', compact(
            'organization',
            'bookings',
            'totalRoomCharge',
            'totalKitchenCharge',
            'totalLaundryCharge',
            'totalBarCharge',
            'totalOtherCharges',
            'totalDiscount',
            'grandTotal',
            'totalPaid',
            'balance'
        ));
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();
        return redirect()->route('organizations.index')->with('success', 'Organization deleted.');
    }
}
