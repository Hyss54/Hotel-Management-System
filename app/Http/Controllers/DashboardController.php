<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\KitchenOrder;
use App\Models\LaundryOrder;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Redirect Front Desk and Housekeeping to their specific "Dashboards"
        if ($user->hasRole('front_desk')) {
            return redirect()->route('frontdesk.index');
        }

        if ($user->hasRole('housekeeping')) {
            return redirect()->route('housekeeping.index');
        }

        $isBar = $user->hasRole('bar');
        $isKitchen = $user->hasRole('kitchen');
        $isLaundry = $user->hasRole('laundry');
        $isRestricted = $isBar || $isKitchen || $isLaundry;

        // Fetch manual charges from bills issued today
        $billsToday = \App\Models\Bill::whereDate('issued_at', today())->get();

        // Base Data
        $data = [
            'total_rooms' => Room::where('is_active', true)->count(),
            'available_rooms' => Room::where('status', 'available')->where('is_active', true)->count(),
            'occupied_rooms' => Room::where('status', 'occupied')->count(),
            'maintenance_rooms' => Room::where('status', 'maintenance')->count(),
            'checkins_today' => Booking::whereDate('checked_in_at', today())->where('status', 'checked_in')->count(),
            'checkouts_today' => Booking::whereDate('checked_out_at', today())->where('status', 'checked_out')->count(),
            'active_guests' => Booking::where('status', 'checked_in')->count(),
            'total_customers' => Customer::count(),

            // Revenue today - default 0
            'accommodation_today' => 0,
            'kitchen_today' => 0,
            'laundry_today' => 0,
            'bar_today' => 0,
            'other_today' => $billsToday->sum('other_charges'),
            'payments_today' => 0,

            // Recent bookings
            'recent_bookings' => Booking::with(['customer', 'room.roomType'])
                ->latest()->limit(8)->get(),
        ];

        // Fetch Data Based on Roles
        if (!$isRestricted || $isKitchen) {
            $data['kitchen_today'] = KitchenOrder::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total_amount');
            // Add manual kitchen charges from bills without bookings (standalone bills)
            $data['kitchen_today'] += \App\Models\Bill::whereDate('issued_at', today())->whereNull('booking_id')->sum('kitchen_charge');
        }

        if (!$isRestricted || $isLaundry) {
            $data['laundry_today'] = LaundryOrder::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total_amount');
            // Add manual laundry charges from standalone bills
            $data['laundry_today'] += \App\Models\Bill::whereDate('issued_at', today())->whereNull('booking_id')->sum('laundry_charge');
        }

        if (!$isRestricted || $isBar) {
            $data['bar_today'] = \App\Models\BarOrder::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total_amount');
            // Add manual bar charges from standalone bills
            $data['bar_today'] += \App\Models\Bill::whereDate('issued_at', today())->whereNull('booking_id')->sum('bar_charge');
        }

        if (!$isRestricted) {
            $data['accommodation_today'] = Booking::where(function ($query) {
                $query->whereDate('checked_out_at', today())->where('status', 'checked_out')
                    ->orWhere(function ($q) {
                        $q->whereDate('checked_in_at', today())->where('status', 'checked_in');
                    });
            })->sum('room_charge');

            // Add manual room charges from standalone bills
            $data['accommodation_today'] += \App\Models\Bill::whereDate('issued_at', today())->whereNull('booking_id')->sum('room_charge');

            $data['payments_today'] = Payment::whereDate('paid_at', today())->sum('amount');
        }

        // Today's collections is the total cash/payment received today
        $data['total_collections_today'] = $data['payments_today'];
        
        // Let's make "Today's Revenue" reflect either Sales or Collections. 
        // Given the user's feedback, they expect to see the money coming in.
        // We'll keep total_revenue_today as the Sales metric but ensure it's calculated clearly.
        $data['total_revenue_today'] = $data['accommodation_today'] + $data['kitchen_today'] + $data['laundry_today'] + $data['bar_today'] + $data['other_today'];

        // Build Chart Data
        $chart_labels = [];
        $chart_values = [];
        $chart_colors = [];

        if (!$isRestricted) {
            $chart_labels[] = 'Accommodation';
            $chart_values[] = $data['accommodation_today'];
            $chart_colors[] = '#3b82f6'; // primary
        }

        if (!$isRestricted || $isKitchen) {
            $chart_labels[] = 'Kitchen';
            $chart_values[] = $data['kitchen_today'];
            $chart_colors[] = '#eab308'; // warning
        }

        if (!$isRestricted || $isLaundry) {
            $chart_labels[] = 'Laundry';
            $chart_values[] = $data['laundry_today'];
            $chart_colors[] = '#10b981'; // success
        }

        if (!$isRestricted || $isBar) {
            $chart_labels[] = 'Bar';
            $chart_values[] = $data['bar_today'];
            $chart_colors[] = '#ef4444'; // danger
        }

        if (!$isRestricted) {
            $chart_labels[] = 'Other';
            $chart_values[] = $data['other_today'];
            $chart_colors[] = '#6366f1'; // indigo
        }

        $data['chart_data'] = [
            'labels' => $chart_labels,
            'values' => $chart_values,
            'colors' => $chart_colors,
        ];

        return view('dashboard', $data);
    }
}
