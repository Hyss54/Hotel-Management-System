@extends('layouts.app')

@section('title', ucfirst($period) . ' Sales Report')
@section('page_title', ucfirst($period) . ' Sales Report')

@section('content')
    <div class="card mb-4 no-print">
        <div class="card-body bg-light">
            <form method="GET" class="row gx-3 gy-2 align-items-center">
                <div class="col-sm-3">
                    <label class="form-label mb-0">Select Period</label>
                    <select name="period" class="form-select">
                        <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label mb-0">Reference Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                </div>
                <div class="col-auto align-self-end">
                    <button type="submit" class="btn btn-primary">Generate Report</button>
                </div>
                <div class="col-auto align-self-end ms-auto">
                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary"><i
                            class="fas fa-print"></i> Print</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row" id="printableReport">
        <div class="col-12 text-center mb-4 d-none d-print-block">
            <h3 class="mb-0">Gold Plus Hotel</h3>
            <h5>{{ ucfirst($period) }} Sales Report</h5>
            <p class="text-muted">{{ $label }}</p>
        </div>

        <!-- Summary Cards -->
        <div class="col-md-2 mb-4">
            <div class="card border-primary h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Accommodation</h6>
                    <h3 class="text-primary mb-0">GH₵{{ number_format($accommodation, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-success h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Kitchen/Restaurant</h6>
                    <h3 class="text-success mb-0">GH₵{{ number_format($kitchen, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-4">
            <div class="card border-info h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Laundry</h6>
                    <h3 class="text-info mb-0">GH₵{{ number_format($laundry, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-4">
            <div class="card border-warning h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Bar</h6>
                    <h3 class="text-warning mb-0">GH₵{{ number_format($bar, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-dark text-white h-100 text-center">
                <div class="card-body border-0">
                    <h6 class="text-white-50 text-uppercase">Total Accrued Revenue</h6>
                    <h2 class="mb-0">GH₵{{ number_format($total, 2) }}</h2>
                </div>
            </div>
        </div>

        <!-- Activity Stats -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-money-check-alt me-2"></i> Actual Payments Received</div>
                <div class="card-body d-flex align-items-center justify-content-center flex-column">
                    <h1 class="display-4 text-success fw-bold">GH₵{{ number_format($payments, 2) }}</h1>
                    <p class="text-muted">Total Cash, Card, and Mobile Money collected today</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-door-open me-2"></i> Room Activity</div>
                <div class="card-body">
                    <div class="d-flex justify-content-around text-center mt-3">
                        <div>
                            <h2 class="text-primary mb-0">{{ $checkIns }}</h2>
                            <span class="text-muted text-uppercase small">Check-ins</span>
                        </div>
                        <div style="width: 1px; background: #e2e8f0;"></div>
                        <div>
                            <h2 class="text-secondary mb-0">{{ $checkOuts }}</h2>
                            <span class="text-muted text-uppercase small">Check-outs</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white;
            }

            #sidebar,
            .top-navbar,
            .card form,
            .btn {
                display: none !important;
            }

            #content {
                margin-left: 0;
                width: 100%;
                border: none !important;
            }

            .card {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                break-inside: avoid;
            }
        }
    </style>
@endsection