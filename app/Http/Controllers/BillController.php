<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with(['customer', 'booking.room'])->latest();
        if ($request->status)
            $query->where('status', $request->status);
        if ($request->search) {
            $query->where('bill_number', 'like', "%{$request->search}%")
                ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }
        $bills = $query->paginate(20)->withQueryString();
        return view('bills.index', compact('bills'));
    }

    public function show(Bill $bill)
    {
        $bill->load(['customer.organization', 'booking.room.roomType', 'payments.receivedBy', 'createdBy']);
        return view('bills.show', compact('bill'));
    }

    public function create(Request $request)
    {
        $bookings = Booking::where('status', 'checked_out')->whereDoesntHave('bill')->with('customer')->get();
        $customers = Customer::where('is_active', true)->get();
        return view('bills.create', compact('bookings', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'room_charge' => 'nullable|numeric|min:0',
            'kitchen_charge' => 'nullable|numeric|min:0',
            'laundry_charge' => 'nullable|numeric|min:0',
            'other_charges' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $booking = $request->booking_id ? Booking::find($request->booking_id) : null;

        // Sum automated charges from booking if linked
        $bookingKitchen = $booking ? $booking->kitchenOrders()->where('status', '!=', 'cancelled')->sum('total_amount') : 0;
        $bookingLaundry = $booking ? $booking->laundryOrders()->where('status', '!=', 'cancelled')->sum('total_amount') : 0;
        $bookingBar = $booking ? $booking->barOrders()->where('status', '!=', 'cancelled')->sum('total_amount') : 0;
        $bookingRoom = $booking ? $booking->room_charge : 0;

        // Combine with manual charges from form
        $roomCharge = $bookingRoom + ($request->room_charge ?? 0);
        $kitchenTotal = $bookingKitchen + ($request->kitchen_charge ?? 0);
        $laundryTotal = $bookingLaundry + ($request->laundry_charge ?? 0);
        $barTotal = $bookingBar; // Bar is currently only from booking link, but could be added manually if needed

        $otherCharges = $request->other_charges ?? 0;
        $discount = $request->discount ?? 0;

        $total = $roomCharge + $kitchenTotal + $laundryTotal + $barTotal + $otherCharges - $discount;

        $bill = Bill::create([
            'bill_number' => Bill::generateBillNumber(),
            'booking_id' => $booking?->id,
            'customer_id' => $request->customer_id,
            'created_by' => auth()->id(),
            'room_charge' => $roomCharge,
            'kitchen_charge' => $kitchenTotal,
            'laundry_charge' => $laundryTotal,
            'bar_charge' => $barTotal,
            'other_charges' => $otherCharges,
            'discount' => $discount,
            'total_amount' => $total,
            'balance' => $total,
            'status' => 'issued',
            'notes' => $request->notes,
            'issued_at' => now(),
        ]);

        return redirect()->route('bills.show', $bill)->with('success', 'Bill generated successfully.');
    }

    public function recordPayment(Request $request, Bill $bill)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,mobile_money,card,bank_transfer',
        ]);

        Payment::create([
            'bill_id' => $bill->id,
            'received_by' => auth()->id(),
            'reference' => 'PAY-' . strtoupper(Str::random(10)),
            'amount' => $request->amount,
            'method' => $request->method,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes,
            'paid_at' => now(),
        ]);

        $totalPaid = $bill->payments()->sum('amount');
        $balance = $bill->total_amount - $totalPaid;
        $status = $balance <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'issued');
        $bill->update(['paid_amount' => $totalPaid, 'balance' => max(0, $balance), 'status' => $status]);

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function invoice(Bill $bill)
    {
        $bill->load(['customer.organization', 'booking.room.roomType', 'payments.receivedBy', 'createdBy']);
        $pdf = Pdf::loadView('bills.invoice', compact('bill'))
            ->setPaper('A4', 'portrait');
        return $pdf->download("Invoice-{$bill->bill_number}.pdf");
    }
}
