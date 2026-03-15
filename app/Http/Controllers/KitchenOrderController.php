<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\KitchenOrder;
use App\Models\KitchenOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KitchenOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = KitchenOrder::with(['customer', 'booking', 'createdBy'])->latest();
        if ($request->status)
            $query->where('status', $request->status);
        $orders = $query->paginate(20)->withQueryString();
        return view('kitchen.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $bookings = Booking::where('status', 'checked_in')->with('customer')->get();
        $selectedBooking = $request->booking_id ? Booking::with('customer')->find($request->booking_id) : null;
        return view('kitchen.create', compact('customers', 'bookings', 'selectedBooking'));
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
        $order = KitchenOrder::create([
            'order_number' => 'KO-' . strtoupper(Str::random(8)),
            'booking_id' => $request->booking_id ?: null,
            'customer_id' => $request->customer_id,
            'created_by' => auth()->id(),
            'notes' => $request->notes,
            'status' => 'pending',
            'total_amount' => 0,
        ]);

        foreach ($request->items as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $total += $itemTotal;
            KitchenOrderItem::create([
                'kitchen_order_id' => $order->id,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $itemTotal,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $order->update(['total_amount' => $total]);
        return redirect()->route('kitchen.show', $order)->with('success', 'Kitchen order created.');
    }

    public function show(KitchenOrder $kitchen)
    {
        $kitchen->load(['customer', 'booking.room', 'items', 'createdBy']);
        return view('kitchen.show', compact('kitchen'));
    }

    public function updateStatus(Request $request, KitchenOrder $kitchen)
    {
        $request->validate(['status' => 'required|in:pending,preparing,served,cancelled']);
        $kitchen->update(['status' => $request->status]);
        return back()->with('success', 'Order status updated.');
    }
}
