<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Incident;
use App\Models\Room;
use App\Models\Customer;
use Illuminate\Http\Request;

class FrontDeskController extends Controller
{
    public function index(Request $request)
    {
        $inHouseGuests = Booking::with(['customer', 'room.roomType'])
            ->where('status', 'checked_in')
            ->get();

        $recentIncidents = Incident::with(['customer', 'room', 'reporter'])
            ->latest()
            ->limit(5)
            ->get();

        $availableRooms = Room::where('status', 'available')->where('is_active', true)->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();

        // Revenue today (Front Desk sees all)
        $accommodation_today = Booking::where(function ($query) {
            $query->whereDate('checked_out_at', today())->where('status', 'checked_out')
                ->orWhere(function ($q) {
                    $q->whereDate('checked_in_at', today())->where('status', 'checked_in');
                });
        })->sum('room_charge');
        $kitchen_today = \App\Models\KitchenOrder::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total_amount');
        $laundry_today = \App\Models\LaundryOrder::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total_amount');
        $bar_today = \App\Models\BarOrder::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total_amount');
        $payments_today = \App\Models\Payment::whereDate('paid_at', today())->sum('amount');
        $total_revenue_today = $accommodation_today + $kitchen_today + $laundry_today + $bar_today;

        // Build Chart Data
        $chart_data = [
            'labels' => ['Accommodation', 'Kitchen', 'Laundry', 'Bar'],
            'values' => [$accommodation_today, $kitchen_today, $laundry_today, $bar_today],
            'colors' => ['#3b82f6', '#eab308', '#10b981', '#ef4444'],
        ];

        return view('frontdesk.portal', compact(
            'inHouseGuests',
            'recentIncidents',
            'availableRooms',
            'occupiedRooms',
            'maintenanceRooms',
            'accommodation_today',
            'kitchen_today',
            'laundry_today',
            'bar_today',
            'payments_today',
            'total_revenue_today',
            'chart_data'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('query');

        $guests = Customer::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        $rooms = Room::where('room_number', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json([
            'guests' => $guests,
            'rooms' => $rooms
        ]);
    }
}
