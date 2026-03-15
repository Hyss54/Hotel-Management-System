<?php

namespace App\Http\Controllers;

use App\Models\BarItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BarItemController extends Controller
{
    public function index()
    {
        Gate::authorize('view-bar-items');

        // Sort: expired first, then expiring soon, then the rest (by name)
        $items = BarItem::orderBy('name')->get()->sortByDesc(function ($item) {
            if ($item->isExpired())
                return 2;
            if ($item->isExpiringSoon())
                return 1;
            return 0;
        })->values();

        return view('bar.items.index', compact('items'));
    }

    public function create()
    {
        Gate::authorize('manage-bar-items');
        return view('bar.items.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-bar-items');

        $request->validate([
            'name' => 'required|string|max:255|unique:bar_items',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        BarItem::create($request->all());

        return redirect()->route('bar-items.index')->with('success', 'Bar item added successfully.');
    }

    public function edit(BarItem $barItem)
    {
        Gate::authorize('manage-bar-items');
        return view('bar.items.edit', compact('barItem'));
    }

    public function show(BarItem $barItem)
    {
        Gate::authorize('view-bar-items');
        return view('bar.items.show', compact('barItem'));
    }

    public function update(Request $request, BarItem $barItem)
    {
        Gate::authorize('manage-bar-items');

        $request->validate([
            'name' => 'required|string|max:255|unique:bar_items,name,' . $barItem->id,
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $barItem->update($request->all());

        return redirect()->route('bar-items.index')->with('success', 'Bar item updated successfully.');
    }

    public function details(BarItem $barItem)
    {
        Gate::authorize('view-bar-items');
        return response()->json($barItem);
    }

    public function destroy(BarItem $barItem)
    {
        Gate::authorize('manage-bar-items');
        $barItem->delete();
        return redirect()->route('bar-items.index')->with('success', 'Bar item deleted.');
    }
}
