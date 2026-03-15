@extends('layouts.app')

@section('title', 'Organizational Receipt')
@section('page_title', 'Receipt: ' . $organization->name)

@section('content')
    <div class="row mb-4 d-print-none">
        <div class="col-12 text-end">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="{{ route('organizations.show', $organization) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Organization
            </a>
        </div>
    </div>

    <div class="card shadow-sm p-4 print-receipt">
        <div class="card-body">
            <div class="row border-bottom pb-4 mb-4">
                <div class="col-sm-6">
                    <x-application-logo class="h-16 w-auto mb-2" />
                    <h2 class="text-primary fw-bold mb-0">GOLD PLUS HOTEL</h2>
                    <p class="text-muted mb-0">123 Hotel Avenue, City, Country</p>
                    <p class="text-muted">Phone: +1 234 567 8900 | Email: info@goldplushotel.com</p>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h3 class="text-uppercase text-muted fw-bold mb-3">Organizational Receipt</h3>
                    <h5 class="mb-0">{{ $organization->name }}</h5>
                    @if($organization->registration_number)
                        <p class="mb-0 text-muted">Reg No: {{ $organization->registration_number }}</p>
                    @endif
                    <p class="mb-0 text-muted">{{ $organization->address }}</p>
                    <p class="mb-0 text-muted">Phone: {{ $organization->phone }}</p>
                    <p class="mb-0 mt-3 text-muted border-top pt-2 d-inline-block">Date: {{ now()->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="fw-bold bg-light p-2 rounded">Occupancy Details</h5>
                    <table class="table table-sm table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>Guest Name</th>
                                <th>Room</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th class="text-center">Nights</th>
                                <th class="text-end">Room Charge</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td>{{ $booking->customer->name }}</td>
                                    <td>{{ $booking->room->room_number }} ({{ $booking->room->roomType->name }})</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) ?: 1 }}
                                    </td>
                                    <td class="text-end">GH₵{{ number_format($booking->bill->room_charge, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No billed bookings found for this
                                        organization.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($bookings->count() > 0)
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-sm table-clear">
                            <tbody>
                                <tr>
                                    <td class="left"><strong>Total Accommodation</strong></td>
                                    <td class="right text-end">GH₵{{ number_format($totalRoomCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="left"><strong>Total Kitchen Services</strong></td>
                                    <td class="right text-end">GH₵{{ number_format($totalKitchenCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="left"><strong>Total Laundry Services</strong></td>
                                    <td class="right text-end">GH₵{{ number_format($totalLaundryCharge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="left"><strong>Total Bar Services</strong></td>
                                    <td class="right text-end">GH₵{{ number_format($totalBarCharge, 2) }}</td>
                                </tr>
                                @if($totalOtherCharges > 0)
                                    <tr>
                                        <td class="left"><strong>Other Charges</strong></td>
                                        <td class="right text-end">GH₵{{ number_format($totalOtherCharges, 2) }}</td>
                                    </tr>
                                @endif
                                @if($totalDiscount > 0)
                                    <tr>
                                        <td class="left text-success"><strong>Total Discount applied</strong></td>
                                        <td class="right text-end text-success">-GH₵{{ number_format($totalDiscount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="table-primary border-primary">
                                    <td class="left border-bottom-0 pb-0">
                                        <h5 class="fw-bold mb-0">Grand Total</h5>
                                    </td>
                                    <td class="right text-end border-bottom-0 pb-0">
                                        <h5 class="fw-bold mb-0">GH₵{{ number_format($grandTotal, 2) }}</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="left border-bottom-0 pt-1 text-success"><strong>Total Paid</strong></td>
                                    <td class="right text-end border-bottom-0 pt-1 text-success">
                                        <strong>GH₵{{ number_format($totalPaid, 2) }}</strong>
                                    </td>
                                </tr>
                                <tr class="{{ $balance > 0 ? 'table-danger' : 'table-success' }}">
                                    <td class="left">
                                        <h5 class="fw-bold mb-0">Balance Due</h5>
                                    </td>
                                    <td class="right text-end">
                                        <h5 class="fw-bold mb-0">GH₵{{ number_format($balance, 2) }}</h5>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="row mt-5">
                <div class="col-8 text-muted small">
                    <p class="mb-1"><strong>Terms & Conditions:</strong></p>
                    <p class="mb-0">This receipt represents an aggregation of individual bills for guests associated with
                        the requested organization. All services are subject to hotel policy. Late checkout may incur
                        additional charges.</p>
                </div>
                <div class="col-4 text-center mt-3 mt-md-0">
                    <div class="border-bottom border-dark mb-2 pb-5"></div>
                    <p class="fw-bold mb-0">Authorized Signature</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .print-receipt,
            .print-receipt * {
                visibility: visible;
            }

            .print-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none !important;
                border: none !important;
            }

            .table-primary {
                background-color: #cfe2ff !important;
                -webkit-print-color-adjust: exact;
            }

            .table-success {
                background-color: #d1e7dd !important;
                -webkit-print-color-adjust: exact;
            }

            .table-danger {
                background-color: #f8d7da !important;
                -webkit-print-color-adjust: exact;
            }

            .bg-light {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
            }

            .text-primary {
                color: #0d6efd !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
@endsection