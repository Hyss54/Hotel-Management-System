@extends('layouts.app')

@section('title', 'Drink Bar Orders')
@section('page_title', 'Drink Bar Orders')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Recent Orders</span>
            <div>
                <a href="{{ route('reports.bar') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fas fa-print"></i> Generate Report
                </a>
                <a href="{{ route('bar.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> New Order
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Order No.</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong><br><small
                                        class="text-muted">{{ $order->created_at->format('M d, H:i') }}</small></td>
                                <td>{{ $order->customer->name }}</td>
                                <td>{{ $order->booking ? 'Room ' . $order->booking->room->room_number : 'Walk-in' }}</td>
                                <td>GH₵{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <form action="{{ route('bar.updateStatus', $order) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()"
                                            style="width: 130px;">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                            </option>
                                            <option value="served" {{ $order->status == 'served' ? 'selected' : '' }}>Served
                                            </option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                                Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('bar.show', $order) }}" class="btn btn-sm btn-info text-white">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No bar orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection