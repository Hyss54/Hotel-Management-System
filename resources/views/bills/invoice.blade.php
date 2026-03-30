<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $bill->bill_number }}</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 2.54cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            color: #333;
            line-height: 1.4;
        }

        .invoice-box {
            width: 100%;
            padding: 0;
        }

        /* Centered logo header */
        .logo-header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #eab308;
        }

        .logo-header img {
            height: 50px;
            margin-bottom: 5px;
        }

        .hotel-name {
            font-size: 20px;
            font-weight: bold;
            color: #0f172a;
        }

        .hotel-info {
            font-size: 12px;
            color: #555;
        }

        /* Invoice meta row */
        .invoice-meta-row {
            width: 100%;
            margin-bottom: 8px;
        }

        .invoice-meta-row td {
            vertical-align: middle;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #3b82f6;
        }

        .invoice-meta {
            font-size: 12px;
            text-align: right;
        }

        /* Details row */
        .details-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .details-table td {
            vertical-align: top;
            font-size: 12px;
        }

        .section-label {
            font-weight: bold;
            font-size: 12px;
            color: #666;
            margin-bottom: 4px;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .items-table th {
            background: #f8fafc;
            padding: 5px 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            font-size: 12px;
        }

        .items-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        .text-right {
            text-align: right !important;
        }

        /* Totals */
        .totals-table {
            width: 50%;
            float: right;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 3px 10px;
            font-size: 12px;
        }

        .totals-table .total-row {
            font-size: 15px;
            font-weight: bold;
            border-top: 2px solid #eab308;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-issued {
            background: #dbeafe;
            color: #1e40af;
        }

        .footer {
            clear: both;
            margin-top: 15px;
            text-align: center;
            color: #777;
            font-size: 11px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">

        {{-- Centered logo --}}
        <div class="logo-header">
            <img src="{{ asset('images/logo.png') }}" alt="Gold Plus Hotel Logo"><br>
            <div class="hotel-name">Gold Plus Hotel</div>
            <div class="hotel-info">Abuakwa, Housing Road, Kumasi Ashanti Region, GPS: AH-1127-2941 | Phone: +233 (0)55 932 5033, +233 (0)36 219 5756</div>
        </div>

        {{-- Invoice meta row --}}
        <table class="invoice-meta-row">
            <tr>
                <td style="width: 50%;">
                    <span class="invoice-title">INVOICE</span>
                </td>
                <td style="width: 50%;" class="invoice-meta">
                    <strong>#{{ $bill->bill_number }}</strong> |
                    Date: {{ $bill->issued_at->format('M d, Y') }} |
                    <span class="status-badge status-{{ $bill->status == 'paid' ? 'paid' : 'issued' }}">
                        {{ $bill->status }}
                    </span>
                </td>
            </tr>
        </table>

        {{-- Billed To / Stay Details --}}
        <table class="details-table">
            <tr>
                <td style="width: 50%;">
                    <div class="section-label">Billed To:</div>
                    {{ $bill->customer->name }}<br>
                    @if($bill->customer->organization)
                        {{ $bill->customer->organization->name }}<br>
                    @endif
                    {{ $bill->customer->phone }}
                    @if($bill->customer->email) | {{ $bill->customer->email }}@endif
                </td>
                <td style="width: 50%;" class="text-right">
                    @if($bill->booking)
                        <div class="section-label" style="text-align: right;">Stay Details:</div>
                        Room: {{ $bill->booking->room->room_number }} ({{ $bill->booking->room->roomType->name }})<br>
                        Check-in: {{ $bill->booking->check_in_date->format('M d, Y') }} |
                        Check-out: {{ $bill->booking->check_out_date->format('M d, Y') }}<br>
                        Duration: {{ $bill->booking->nights }} Night(s)
                    @endif
                </td>
            </tr>
        </table>

        {{-- Items --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Amount (GHS)</th>
                </tr>
            </thead>
            <tbody>
                @if($bill->room_charge > 0)
                    <tr>
                        <td>Accommodation Charge ({{ $bill->booking?->nights }} nights)</td>
                        <td class="text-right">GH₵{{ number_format($bill->room_charge, 2) }}</td>
                    </tr>
                @endif
                @if($bill->kitchen_charge > 0)
                    <tr>
                        <td>Restaurant & Kitchen Services</td>
                        <td class="text-right">GH₵{{ number_format($bill->kitchen_charge, 2) }}</td>
                    </tr>
                @endif
                @if($bill->laundry_charge > 0)
                    <tr>
                        <td>Laundry & Cleaning Services</td>
                        <td class="text-right">GH₵{{ number_format($bill->laundry_charge, 2) }}</td>
                    </tr>
                @endif
                @if($bill->bar_charge > 0)
                    <tr>
                        <td>Drink Bar Orders</td>
                        <td class="text-right">GH₵{{ number_format($bill->bar_charge, 2) }}</td>
                    </tr>
                @endif
                @if($bill->other_charges > 0)
                    <tr>
                        <td>Other Charges</td>
                        <td class="text-right">GH₵{{ number_format($bill->other_charges, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Totals --}}
        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">GH₵{{ number_format($bill->total_amount + $bill->discount, 2) }}</td>
            </tr>
            @if($bill->discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-GH₵{{ number_format($bill->discount, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total Amount:</td>
                <td class="text-right">GH₵{{ number_format($bill->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Paid Amount:</td>
                <td class="text-right">GH₵{{ number_format($bill->paid_amount, 2) }}</td>
            </tr>
            <tr>
                <td style="color: #ef4444; font-weight: bold;">Balance Due:</td>
                <td class="text-right" style="color: #ef4444; font-weight: bold;">
                    GH₵{{ number_format($bill->balance, 2) }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for choosing Gold Plus Hotel!</p>
            <p>Generated by {{ $bill->createdBy->name ?? 'System' }} on {{ now()->format('M d, Y H:i') }}</p>
        </div>
    </div>
</body>

</html>