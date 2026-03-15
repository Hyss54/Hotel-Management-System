<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\BarOrder;
use App\Models\BarOrderItem;
use App\Models\BarItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BarOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = BarOrder::with(['customer', 'booking', 'createdBy'])->latest();
        if ($request->status)
            $query->where('status', $request->status);
        $orders = $query->paginate(20)->withQueryString();
        return view('bar.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $bookings = Booking::where('status', 'checked_in')->with('customer')->get();
        $selectedBooking = $request->booking_id ? Booking::with('customer')->find($request->booking_id) : null;
        $barItems = BarItem::where('is_active', true)->orderBy('name')->get();
        return view('bar.create', compact('customers', 'bookings', 'selectedBooking', 'barItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $total = 0;
        $order = BarOrder::create([
            'order_number' => 'BO-' . strtoupper(Str::random(8)),
            'booking_id' => $request->booking_id ?: null,
            'customer_id' => $request->customer_id,
            'created_by' => auth()->id(),
            'notes' => $request->notes,
            'status' => 'pending',
            'total_amount' => 0,
        ]);

        foreach ($request->items as $item) {
            $barItem = null;
            if (isset($item['bar_item_id']) && $item['bar_item_id']) {
                $barItem = BarItem::find($item['bar_item_id']);
                if (!$barItem || $barItem->stock_quantity < $item['quantity']) {
                    $order->delete(); // Rollback order creation
                    return back()->with('error', 'Insufficient stock for ' . ($barItem ? $barItem->name : 'item'));
                }
            }

            $itemTotal = $item['quantity'] * $item['unit_price'];
            $total += $itemTotal;
            BarOrderItem::create([
                'bar_order_id' => $order->id,
                'bar_item_id' => $item['bar_item_id'] ?? null,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $itemTotal,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $order->update(['total_amount' => $total]);
        return redirect()->route('bar.show', $order)->with('success', 'Bar order created.');
    }

    public function show(BarOrder $bar)
    {
        $bar->load(['customer', 'booking.room', 'items', 'createdBy']);
        return view('bar.show', compact('bar'));
    }

    public function updateStatus(Request $request, BarOrder $bar)
    {
        $request->validate(['status' => 'required|in:pending,served,cancelled']);

        $oldStatus = $bar->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return back();
        }

        // Deduct stock if moving TO served
        if ($newStatus === 'served') {
            foreach ($bar->items as $item) {
                if ($item->bar_item_id) {
                    $barItem = $item->barItem;
                    if ($barItem->stock_quantity < $item->quantity) {
                        return back()->with('error', "Insufficient stock for {$barItem->name}.");
                    }
                    $barItem->decrement('stock_quantity', $item->quantity);
                }
            }
        }

        // Return stock if moving FROM served (to cancelled or pending)
        if ($oldStatus === 'served' && ($newStatus === 'cancelled' || $newStatus === 'pending')) {
            foreach ($bar->items as $item) {
                if ($item->bar_item_id) {
                    $item->barItem->increment('stock_quantity', $item->quantity);
                }
            }
        }

        $bar->update(['status' => $newStatus]);
        return back()->with('success', 'Order status updated.');
    }
}
