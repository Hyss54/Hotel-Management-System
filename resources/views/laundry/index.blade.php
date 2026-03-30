@extends('layouts.app')

@section('title', 'Laundry Orders')
@section('page_title', 'Laundry Management')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Recent Laundry Requests</span>
            <div>
                <a href="{{ route('reports.laundry') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fas fa-print"></i> Generate Report
                </a>
                <a href="{{ route('laundry.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> New Order
                </a>
            </div>
        </div>

        <div class="card-body border-bottom bg-light">
            <form method="GET" class="row gx-2 gy-2 align-items-center">
                <div class="col-auto">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Order No or Guest"
                        value="{{ request('search') }}">
                </div>
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing
                        </option>
                        <option value="ready" {{ request('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Order No.</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                    <br><small class="text-muted">{{ $order->created_at->format('M d, H:i') }}</small>
                                </td>
                                <td>{{ $order->customer->name }}</td>
                                <td>{{ $order->booking ? 'Room ' . $order->booking->room->room_number : 'Walk-in' }}</td>
                                <td>
                                    <form action="{{ route('laundry.updateStatus', $order) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()"
                                            style="width: 130px;">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending
                                            </option>
                                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                                Processing</option>
                                            <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Ready</option>
                                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>
                                                Delivered</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                                Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                                <td>GH₵{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('laundry.show', $order) }}"
                                        class="btn btn-sm btn-info text-white">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No laundry orders found.</td>
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