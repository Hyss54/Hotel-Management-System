@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-uppercase text-white-50">Total Rooms</h6>
                    <h2 class="mb-0">{{ $total_rooms }}</h2>
                    <i class="fas fa-bed icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white">
                <div class="card-body">
                    <h6 class="text-uppercase text-white-50">Available</h6>
                    <h2 class="mb-0">{{ $available_rooms }}</h2>
                    <i class="fas fa-check-circle icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-danger text-white">
                <div class="card-body">
                    <h6 class="text-uppercase text-white-50">Occupied</h6>
                    <h2 class="mb-0">{{ $occupied_rooms }}</h2>
                    <i class="fas fa-door-closed icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="text-uppercase text-dark-50">Active Guests</h6>
                    <h2 class="mb-0">{{ $active_guests }}</h2>
                    <i class="fas fa-users icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <div class="card stat-card bg-info text-white">
                <div class="card-body">
                    <h6 class="text-uppercase text-white-50">Check-ins Today</h6>
                    <h2 class="mb-0">{{ $checkins_today }}</h2>
                    <i class="fas fa-sign-in-alt icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-secondary text-white">
                <div class="card-body">
                    <h6 class="text-uppercase text-white-50">Check-outs Today</h6>
                    <h2 class="mb-0">{{ $checkouts_today }}</h2>
                    <i class="fas fa-sign-out-alt icon"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card bg-dark text-white">
                <div class="card-body">
                    <h6 class="text-uppercase text-white-50">Today's Revenue</h6>
                    <h2 class="mb-0">GH₵{{ number_format($total_revenue_today, 2) }}</h2>
                    <i class="fas fa-money-bill-wave icon"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-chart-pie me-2"></i> Today's Revenue Breakdown</span>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header">
                    <span><i class="fas fa-clock me-2"></i> Recent Bookings</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recent_bookings as $booking)
                            <a href="{{ route('bookings.show', $booking) }}"
                                class="list-group-item list-group-item-action py-3">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $booking->customer->name }}</h6>
                                    <small class="text-muted">{{ $booking->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 small">Room {{ $booking->room->room_number }}
                                    ({{ $booking->room->roomType->name }})</p>
                                <small
                                    class="badge bg-{{ $booking->status == 'checked_in' ? 'success' : ($booking->status == 'reserved' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </small>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">No recent bookings</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const chartData = {!! json_encode($chart_data ?? []) !!};

        // If there's no data (or all values are 0), show a dummy slice to prevent empty chart
        let displayData = chartData.values;
        let displayColors = chartData.colors;
        let displayLabels = chartData.labels;

        if (!displayData || displayData.reduce((a, b) => a + Number(b), 0) === 0) {
            displayData = [1];
            displayColors = ['#e2e8f0'];
            displayLabels = ['No Revenue Today'];
        }

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: displayLabels,
                datasets: [{
                    data: displayData,
                    backgroundColor: displayColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label !== 'No Revenue Today') {
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += new Intl.NumberFormat('en-GH', { style: 'currency', currency: 'GHS' }).format(context.parsed);
                                    }
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush