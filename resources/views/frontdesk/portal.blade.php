@extends('layouts.app')

@section('title', 'Front Desk Portal')
@section('page_title', 'Front Desk Command Center')

@section('content')
    <div class="row">
        <!-- Quick Overview Stats -->
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Available Rooms</h6>
                    <h2 class="mb-0">{{ $availableRooms }}</h2>
                    <i class="fas fa-door-open position-absolute top-50 end-0 translate-middle-y me-3 opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Occupied Rooms</h6>
                    <h2 class="mb-0">{{ $occupiedRooms }}</h2>
                    <i
                        class="fas fa-user-check position-absolute top-50 end-0 translate-middle-y me-3 opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">In-House Guests</h6>
                    <h2 class="mb-0">{{ $inHouseGuests->count() }}</h2>
                    <i class="fas fa-users position-absolute top-50 end-0 translate-middle-y me-3 opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">Maintenance</h6>
                    <h2 class="mb-0">{{ $maintenanceRooms }}</h2>
                    <i class="fas fa-tools position-absolute top-50 end-0 translate-middle-y me-3 opacity-25 fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Revenue Breakdown -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i> Today's Revenue Breakdown (Total:
                        GH₵{{ number_format($total_revenue_today, 2) }})</h5>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <!-- In-House Guest Management -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">In-House Guest Management</h5>
                    <div class="input-group input-group-sm w-50">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="guestSearch" class="form-control bg-light border-start-0"
                            placeholder="Search Guest or Room...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="guestTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Room</th>
                                    <th>Guest Name</th>
                                    <th>Check-out</th>
                                    <th>Quick Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inHouseGuests as $booking)
                                    <tr>
                                        <td><span class="badge bg-light text-dark shadow-sm">Room
                                                {{ $booking->room->room_number }}</span></td>
                                        <td>
                                            <strong>{{ $booking->customer->name }}</strong><br>
                                            <small class="text-muted">{{ $booking->customer->phone }}</small>
                                        </td>
                                        <td>{{ $booking->check_out_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-primary"
                                                    title="Manage Booking">
                                                    <i class="fas fa-calendar-check"></i>
                                                </a>
                                                @can('manage-incidents')
                                                <a href="{{ route('incidents.create', ['customer_id' => $booking->customer_id, 'room_id' => $booking->room_id, 'booking_id' => $booking->id]) }}"
                                                    class="btn btn-outline-danger" title="Report Incident">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </a>
                                                @endcan
                                                <a href="{{ route('kitchen.create', ['booking_id' => $booking->id]) }}"
                                                    class="btn btn-outline-success" title="Add Kitchen Order">
                                                    <i class="fas fa-utensils"></i>
                                                </a>
                                                <a href="{{ route('bar.create', ['booking_id' => $booking->id]) }}"
                                                    class="btn btn-outline-info" title="Add Bar Order">
                                                    <i class="fas fa-glass-martini-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i><br>
                                            No guests currently checked in.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Incidents -->
        <div class="col-md-4">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Incidents</h5>
                    @can('manage-incidents')
                    <a href="{{ route('incidents.create') }}" class="btn btn-link text-white p-0">
                        <i class="fas fa-plus-circle"></i>
                    </a>
                    @endcan
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentIncidents as $incident)
                            <div
                                class="list-group-item border-start border-4 border-{{ $incident->severity == 'emergency' ? 'danger' : ($incident->severity == 'high' ? 'warning' : 'info') }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold">{{ $incident->title }}</h6>
                                    <small class="text-muted">{{ $incident->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 small text-truncate">{{ $incident->description }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span
                                        class="badge rounded-pill bg-{{ $incident->status == 'open' ? 'danger' : 'success' }} small">
                                        {{ ucfirst($incident->status) }}
                                    </span>
                                    <small class="text-muted">Room {{ $incident->room->room_number ?? 'N/A' }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-check-circle fa-2x mb-2 text-success opacity-25"></i><br>
                                All quiet. No recent incidents.
                            </div>
                        @endforelse
                    </div>
                </div>
                @if($recentIncidents->count() > 0)
                    <div class="card-footer bg-white text-center">
                        <a href="{{ route('incidents.index') }}" class="small text-decoration-none">View All Incidents</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.getElementById('guestSearch').addEventListener('keyup', function () {
            let filter = this.value.toUpperCase();
            let trs = document.getElementById('guestTable').getElementsByTagName('tr');

            for (let i = 1; i < trs.length; i++) {
                let text = trs[i].textContent.toUpperCase();
                trs[i].style.display = text.indexOf(filter) > -1 ? '' : 'none';
            }
        });
    </script>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartCanvas = document.getElementById('revenueChart');
            if (chartCanvas) {
                const ctx = chartCanvas.getContext('2d');
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
            }
        });
    </script>
@endpush