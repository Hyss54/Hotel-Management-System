<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\LaundryItem;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventorySynchronizationController extends Controller
{
    /**
     * Housekeeping sends dirty items to Laundry (creates pending request).
     */
    public function collectDirty(Request $request)
    {
        $request->validate([
            'laundry_item_id' => 'required|exists:laundry_items,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $item = LaundryItem::findOrFail($request->laundry_item_id);
        $housekeeping = Department::where('name', 'Housekeeping')->first();
        $laundry = Department::where('name', 'Laundry')->first();

        InventoryTransaction::create([
            'laundry_item_id' => $item->id,
            'from_department_id' => $housekeeping->id,
            'to_department_id' => $laundry->id,
            'quantity' => $request->quantity,
            'transaction_type' => 'dirty_collection',
            'status' => 'pending',
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Return submitted. Awaiting Laundry approval.');
    }

    /**
     * Housekeeping requests clean items from Laundry (creates pending request).
     */
    public function requestClean(Request $request)
    {
        $request->validate([
            'laundry_item_id' => 'required|exists:laundry_items,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $item = LaundryItem::findOrFail($request->laundry_item_id);
        $housekeeping = Department::where('name', 'Housekeeping')->first();
        $laundry = Department::where('name', 'Laundry')->first();

        InventoryTransaction::create([
            'laundry_item_id' => $item->id,
            'from_department_id' => $laundry->id,
            'to_department_id' => $housekeeping->id,
            'quantity' => $request->quantity,
            'transaction_type' => 'clean_delivery',
            'status' => 'pending',
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Request submitted. Awaiting Laundry approval.');
    }

    /**
     * Laundry approves a pending request from Housekeeping.
     */
    public function approveRequest(InventoryTransaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $item = $transaction->item;

        if ($transaction->transaction_type === 'clean_delivery') {
            if ($item->stock_quantity < $transaction->quantity) {
                return redirect()->back()->with('error', 'Insufficient available stock to approve this request.');
            }
            DB::transaction(function () use ($item, $transaction) {
                $item->decrement('stock_quantity', $transaction->quantity);
                $item->increment('in_housekeeping_quantity', $transaction->quantity);
                $transaction->update(['status' => 'approved']);
            });
        } elseif ($transaction->transaction_type === 'dirty_collection') {
            DB::transaction(function () use ($item, $transaction) {
                $item->decrement('in_housekeeping_quantity', $transaction->quantity);
                $item->increment('stock_quantity', $transaction->quantity);
                $transaction->update(['status' => 'approved']);
            });
        }

        return redirect()->back()->with('success', 'Request approved. ' . $transaction->quantity . ' items processed.');
    }

    /**
     * Laundry denies a pending request from Housekeeping.
     */
    public function denyRequest(Request $request, InventoryTransaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'This request has already been processed.');
        }

        $transaction->update([
            'status' => 'denied',
            'notes' => $transaction->notes . ' | Denied: ' . ($request->reason ?? 'No reason given'),
        ]);

        return redirect()->back()->with('success', 'Request denied.');
    }


    /**
     * Laundry marks dirty items as clean (washed).
     */
    public function markAsClean(Request $request)
    {
        $request->validate([
            'laundry_item_id' => 'required|exists:laundry_items,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $item = LaundryItem::findOrFail($request->laundry_item_id);

        if ($item->dirty_quantity < $request->quantity) {
            return redirect()->back()->with('error', 'Insufficient dirty items to wash.');
        }

        $laundry = Department::where('name', 'Laundry')->first();

        DB::transaction(function () use ($item, $request, $laundry) {
            // Update item counts
            $item->decrement('dirty_quantity', $request->quantity);
            $item->increment('stock_quantity', $request->quantity);

            // Record transaction
            InventoryTransaction::create([
                'laundry_item_id' => $item->id,
                'from_department_id' => $laundry->id,
                'to_department_id' => $laundry->id, // Staying in Laundry
                'quantity' => $request->quantity,
                'transaction_type' => 'wash_complete',
                'status' => 'completed',
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', 'Items marked as clean successfully.');
    }

    /**
     * Restock: increase available stock quantity for an item.
     */
    public function restock(Request $request)
    {
        $request->validate([
            'laundry_item_id' => 'required|exists:laundry_items,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $item = LaundryItem::findOrFail($request->laundry_item_id);
        $laundry = Department::where('name', 'Laundry')->first();

        DB::transaction(function () use ($item, $request, $laundry) {
            $item->increment('stock_quantity', $request->quantity);

            InventoryTransaction::create([
                'laundry_item_id' => $item->id,
                'from_department_id' => null,
                'to_department_id' => $laundry->id,
                'quantity' => $request->quantity,
                'transaction_type' => 'restock',
                'status' => 'completed',
                'notes' => $request->notes ?? 'New stock added',
                'created_by' => Auth::id(),
            ]);
        });

        return redirect()->back()->with('success', $request->quantity . ' units added to ' . $item->name . '.');
    }

    /**
     * Printable request report.
     */
    public function requestReport(Request $request)
    {
        $query = InventoryTransaction::with(['item', 'creator', 'fromDepartment', 'toDepartment']);

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->type) {
            $query->where('transaction_type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->get();

        return view('laundry.request-report', compact('transactions'));
    }
}
