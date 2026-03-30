<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\HousekeepingTask;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['customer', 'room.roomType'])->latest();
        if ($request->status)
            $query->where('status', $request->status);
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_number', 'like', "%{$request->search}%")
                    ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$request->search}%"));
            });
        }
        $bookings = $query->paginate(20)->withQueryString();
        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $rooms = Room::with('roomType')->where('status', 'available')->where('is_active', true)->get();
        $roomTypes = RoomType::where('is_active', true)->get();
        return view('bookings.create', compact('customers', 'rooms', 'roomTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults' => 'required|integer|min:1',
        ]);

        $room = Room::findOrFail($request->room_id);
        if ($room->status !== 'available') {
            return back()->withErrors(['room_id' => 'Selected room is not available.'])->withInput();
        }

        $checkIn = \Carbon\Carbon::parse($request->check_in_date);
        $checkOut = \Carbon\Carbon::parse($request->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        $roomCharge = $room->roomType->price_per_night * $nights;

        $booking = Booking::create([
            'booking_number' => 'BK-' . strtoupper(Str::random(8)),
            'customer_id' => $request->customer_id,
            'room_id' => $request->room_id,
            'created_by' => auth()->id(),
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'nights' => $nights,
            'room_charge' => $roomCharge,
            'adults' => $request->adults,
            'children' => $request->children ?? 0,
            'status' => 'reserved',
            'special_requests' => $request->special_requests,
        ]);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking created successfully.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['customer.organization', 'room.roomType', 'kitchenOrders.items', 'laundryOrders.items', 'barOrders.items', 'bill.payments', 'createdBy']);
        return view('bookings.show', compact('booking'));
    }

    public function checkIn(Booking $booking)
    {
        if ($booking->status !== 'reserved') {
            return back()->with('error', 'Only reserved bookings can be checked in.');
        }
        $booking->update(['status' => 'checked_in', 'checked_in_at' => now()]);
        $booking->room->update(['status' => 'occupied']);
        return back()->with('success', 'Guest checked in successfully.');
    }

    public function checkOut(Booking $booking)
    {
        if ($booking->status !== 'checked_in') {
            return back()->with('error', 'Only checked-in bookings can be checked out.');
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($booking) {
                $booking->update([
                    'status' => 'checked_out',
                    'checked_out_at' => now()
                ]);

                // Mark room as dirty and schedule cleaning
                $booking->room->update(['status' => 'dirty']);

                HousekeepingTask::create([
                    'room_id' => $booking->room_id,
                    'task_type' => 'Check-out Cleaning',
                    'status' => 'pending',
                    'created_by' => auth()->id() ?? 1,
                    'notes' => 'Automatic task created upon check-out of ' . $booking->booking_number,
                ]);

                // Auto-generate bill if not existing
                if (!$booking->bill) {
                    $kitchenTotal = $booking->kitchenOrders()->where('status', '!=', 'cancelled')->sum('total_amount');
                    $laundryTotal = $booking->laundryOrders()->where('status', '!=', 'cancelled')->sum('total_amount');
                    $barTotal = $booking->barOrders()->where('status', '!=', 'cancelled')->sum('total_amount');
                    $total = $booking->room_charge + $kitchenTotal + $laundryTotal + $barTotal;

                    Bill::create([
                        'bill_number' => Bill::generateBillNumber(),
                        'booking_id' => $booking->id,
                        'customer_id' => $booking->customer_id,
                        'created_by' => auth()->id() ?? 1,
                        'room_charge' => $booking->room_charge,
                        'kitchen_charge' => $kitchenTotal,
                        'laundry_charge' => $laundryTotal,
                        'bar_charge' => $barTotal,
                        'total_amount' => $total,
                        'balance' => $total,
                        'status' => 'issued',
                        'issued_at' => now(),
                    ]);
                }
            });

            return redirect()->route('bookings.show', $booking)->with('success', 'Guest checked out successfully. Bill generated and room marked for cleaning.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Checkout failed for booking ' . $booking->id . ': ' . $e->getMessage());
            return back()->with('error', 'An error occurred during checkout: ' . $e->getMessage());
        }
    }

    public function destroy(Booking $booking)
    {
        if (in_array($booking->status, ['checked_in', 'checked_out'])) {
            return back()->with('error', 'Cannot cancel an active booking.');
        }
        $booking->update(['status' => 'cancelled']);
        return redirect()->route('bookings.index')->with('success', 'Booking cancelled.');
    }
}
