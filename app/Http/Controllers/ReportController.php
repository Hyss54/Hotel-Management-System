<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\KitchenOrder;
use App\Models\LaundryOrder;
use App\Models\BarOrder;
use App\Models\Payment;
use App\Models\Organization;
use App\Models\Room;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function dailySales(Request $request)
    {
        $date = $request->date ? \Carbon\Carbon::parse($request->date) : today();
        $period = $request->period ?? 'daily';

        switch ($period) {
            case 'weekly':
                $start = $date->copy()->startOfWeek();
                $end = $date->copy()->endOfWeek();
                $label = "Week of " . $start->format('M d') . ' - ' . $end->format('M d, Y');
                break;
            case 'monthly':
                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();
                $label = $start->format('F Y');
                break;
            case 'yearly':
                $start = $date->copy()->startOfYear();
                $end = $date->copy()->endOfYear();
                $label = $start->format('Y');
                break;
            default:
                $start = $date->copy()->startOfDay();
                $end = $date->copy()->endOfDay();
                $label = $date->format('F d, Y');
                break;
        }

        $data = [
            'date' => $date,
            'period' => $period,
            'label' => $label,
            'accommodation' => Booking::whereBetween('checked_out_at', [$start, $end])->where('status', 'checked_out')->sum('room_charge'),
            'kitchen' => KitchenOrder::whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled')->sum('total_amount'),
            'laundry' => LaundryOrder::whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled')->sum('total_amount'),
            'bar' => BarOrder::whereBetween('created_at', [$start, $end])->where('status', '!=', 'cancelled')->sum('total_amount'),
            'payments' => Payment::whereBetween('paid_at', [$start, $end])->sum('amount'),
            'checkIns' => Booking::whereBetween('checked_in_at', [$start, $end])->count(),
            'checkOuts' => Booking::whereBetween('checked_out_at', [$start, $end])->count(),
        ];
        $data['total'] = $data['accommodation'] + $data['kitchen'] + $data['laundry'] + $data['bar'];
        return view('reports.daily-sales', $data);
    }

    public function accommodation(Request $request)
    {
        $from = $request->from ? \Carbon\Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->to ? \Carbon\Carbon::parse($request->to) : now()->endOfMonth();
        $bookings = Booking::with(['customer', 'room.roomType'])
            ->whereBetween('check_in_date', [$from, $to])
            ->latest()->paginate(30)->withQueryString();
        return view('reports.accommodation', compact('bookings', 'from', 'to'));
    }

    public function department(Request $request)
    {
        $from = $request->from ? \Carbon\Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->to ? \Carbon\Carbon::parse($request->to) : now()->endOfMonth();
        $data = [
            'from' => $from,
            'to' => $to,
            'accommodation' => Booking::whereBetween('check_in_date', [$from, $to])->where('status', '!=', 'cancelled')->sum('room_charge'),
            'kitchen' => KitchenOrder::whereBetween('created_at', [$from, $to])->where('status', '!=', 'cancelled')->sum('total_amount'),
            'laundry' => LaundryOrder::whereBetween('created_at', [$from, $to])->where('status', '!=', 'cancelled')->sum('total_amount'),
            'bar' => BarOrder::whereBetween('created_at', [$from, $to])->where('status', '!=', 'cancelled')->sum('total_amount'),
        ];
        $data['total'] = $data['accommodation'] + $data['kitchen'] + $data['laundry'] + $data['bar'];
        return view('reports.department', $data);
    }

    public function customers(Request $request)
    {
        $query = Customer::withCount('bookings')->with('organization')->latest();
        if ($request->search)
            $query->where('name', 'like', "%{$request->search}%");
        $customers = $query->paginate(30)->withQueryString();
        return view('reports.customers', compact('customers'));
    }

    public function kitchen(Request $request)
    {
        $from = $request->from ? \Carbon\Carbon::parse($request->from)->startOfDay() : today()->startOfDay();
        $to = $request->to ? \Carbon\Carbon::parse($request->to)->endOfDay() : today()->endOfDay();
        $organization_id = $request->organization_id;

        $organizations = Organization::orderBy('name')->get();
        
        $query = KitchenOrder::with(['customer', 'booking.room'])
            ->whereBetween('created_at', [$from, $to])
            ->latest();

        if ($organization_id) {
            $query->whereHas('customer', function ($q) use ($organization_id) {
                $q->where('organization_id', $organization_id);
            });
        }
            
        $orders = $query->get();
        $selected_organization = $organization_id ? Organization::find($organization_id) : null;
            
        return view('reports.kitchen', compact('orders', 'from', 'to', 'organizations', 'organization_id', 'selected_organization'));
    }

    public function bar(Request $request)
    {
        $from = $request->from ? \Carbon\Carbon::parse($request->from)->startOfDay() : today()->startOfDay();
        $to = $request->to ? \Carbon\Carbon::parse($request->to)->endOfDay() : today()->endOfDay();
        $organization_id = $request->organization_id;

        $organizations = Organization::orderBy('name')->get();
        
        $query = BarOrder::with(['customer', 'booking.room'])
            ->whereBetween('created_at', [$from, $to])
            ->latest();

        if ($organization_id) {
            $query->whereHas('customer', function ($q) use ($organization_id) {
                $q->where('organization_id', $organization_id);
            });
        }
            
        $orders = $query->get();
        $selected_organization = $organization_id ? Organization::find($organization_id) : null;
            
        return view('reports.bar', compact('orders', 'from', 'to', 'organizations', 'organization_id', 'selected_organization'));
    }

    public function laundry(Request $request)
    {
        $from = $request->from ? \Carbon\Carbon::parse($request->from)->startOfDay() : today()->startOfDay();
        $to = $request->to ? \Carbon\Carbon::parse($request->to)->endOfDay() : today()->endOfDay();
        $organization_id = $request->organization_id;

        $organizations = Organization::orderBy('name')->get();
        
        $query = LaundryOrder::with(['customer', 'booking.room'])
            ->whereBetween('created_at', [$from, $to])
            ->latest();

        if ($organization_id) {
            $query->whereHas('customer', function ($q) use ($organization_id) {
                $q->where('organization_id', $organization_id);
            });
        }
            
        $orders = $query->get();
        $selected_organization = $organization_id ? Organization::find($organization_id) : null;
            
        return view('reports.laundry', compact('orders', 'from', 'to', 'organizations', 'organization_id', 'selected_organization'));
    }
}
