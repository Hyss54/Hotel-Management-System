<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\LaundryItem;
use App\Models\InventoryTransaction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LaundryItemController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $this->authorize('view-laundry-items');
        $query = LaundryItem::query();
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        $items = $query->latest()->paginate(20);
        $pendingRequests = InventoryTransaction::with(['item', 'creator'])
            ->where('status', 'pending')
            ->latest()
            ->get();
        return view('laundry.items.index', compact('items', 'pendingRequests'));
    }

    public function create()
    {
        $this->authorize('manage-laundry-items');
        return view('laundry.items.create');
    }

    public function store(Request $request)
    {
        $this->authorize('manage-laundry-items');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'min_stock_level' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        LaundryItem::create($validated);

        return redirect()->route('laundry-items.index')
            ->with('success', 'Laundry item created successfully.');
    }

    public function edit(LaundryItem $laundryItem)
    {
        $this->authorize('manage-laundry-items');
        return view('laundry.items.edit', compact('laundryItem'));
    }

    public function update(Request $request, LaundryItem $laundryItem)
    {
        $this->authorize('manage-laundry-items');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stock_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'min_stock_level' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $laundryItem->update($validated);

        return redirect()->route('laundry-items.index')
            ->with('success', 'Laundry item updated successfully.');
    }

    public function destroy(LaundryItem $laundryItem)
    {
        $this->authorize('manage-laundry-items');
        $laundryItem->delete();
        return redirect()->route('laundry-items.index')
            ->with('success', 'Laundry item deleted successfully.');
    }
}
