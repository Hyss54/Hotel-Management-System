<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Organization;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('organization')->latest();
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('phone', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        $customers = $query->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create(Request $request)
    {
        $organizations = Organization::where('is_active', true)->orderBy('name')->get();
        $selectedOrganizationId = $request->organization_id;
        return view('customers.create', compact('organizations', 'selectedOrganizationId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'type' => 'required|in:individual,organization',
            'organization_id' => 'nullable|exists:organizations,id',
            'id_number' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);
        Customer::create($request->all());
        return redirect()->route('customers.index')->with('success', 'Customer registered successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['organization', 'bookings.room.roomType', 'bills.payments']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $organizations = Organization::where('is_active', true)->get();
        return view('customers.edit', compact('customer', 'organizations'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'type' => 'required|in:individual,organization',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);
        $customer->update($request->all());
        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
