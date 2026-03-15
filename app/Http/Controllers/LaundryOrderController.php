<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LaundryOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = LaundryOrder::with(['customer', 'booking', 'createdBy'])->latest();
        if ($request->status)
            $query->where('status', $request->status);
        $orders = $query->paginate(20)->withQueryString();
        return view('laundry.index', compact('orders'));
    }

    public function create(Request $request)
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $bookings = Booking::where('status', 'checked_in')->with('customer')->get();
        $selectedBooking = $request->booking_id ? Booking::with('customer')->find($request->booking_id) : null;
        return view('laundry.create', compact('customers', 'bookings', 'selectedBooking'));
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
        $order = LaundryOrder::create([
            'order_number' => 'LO-' . strtoupper(Str::random(8)),
            'booking_id' => $request->booking_id ?: null,
            'customer_id' => $request->customer_id,
            'created_by' => auth()->id(),
            'notes' => $request->notes,
            'due_date' => $request->due_date,
            'status' => 'pending',
            'total_amount' => 0,
        ]);

        foreach ($request->items as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $total += $itemTotal;
            LaundryOrderItem::create([
                'laundry_order_id' => $order->id,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $itemTotal,
                'notes' => $item['notes'] ?? null,
            ]);
        }

        $order->update(['total_amount' => $total]);
        return redirect()->route('laundry.show', $order)->with('success', 'Laundry order created.');
    }

    public function show(LaundryOrder $laundry)
    {
        $laundry->load(['customer', 'booking.room', 'items', 'createdBy']);
        return view('laundry.show', compact('laundry'));
    }

    public function updateStatus(Request $request, LaundryOrder $laundry)
    {
        $request->validate(['status' => 'required|in:pending,in_progress,done,delivered,cancelled']);
        $laundry->update(['status' => $request->status]);
        return back()->with('success', 'Order status updated.');
    }
}
