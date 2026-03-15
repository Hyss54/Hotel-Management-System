<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $bill->bill_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #333;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
        }

        .header {
            width: 100%;
            border-bottom: 2px solid #eab308;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header table {
            width: 100%;
        }

        .hotel-name {
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #3b82f6;
            text-align: right;
        }

        .details-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .details-table td {
            vertical-align: top;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background: #f8fafc;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .text-right {
            text-align: right !important;
        }

        .totals-table {
            width: 50%;
            float: right;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px;
        }

        .totals-table .total-row {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #eab308;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
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
            margin-top: 50px;
            text-align: center;
            color: #777;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="header">
            <table>
                <tr>
                    <td>
                        <div class="hotel-name">Gold Plus Hotel</div>
                        <div>123 Luxury Avenue, Metropolis</div>
                        <div>Phone: +1 234 567 890</div>
                        <div>Email: info@goldplushotel.com</div>
                    </td>
                    <td class="text-right">
                        <div class="invoice-title">INVOICE</div>
                        <div><strong>#{{ $bill->bill_number }}</strong></div>
                        <div>Date: {{ $bill->issued_at->format('M d, Y') }}</div>
                        <div style="margin-top: 5px;">
                            <span class="status-badge status-{{ $bill->status == 'paid' ? 'paid' : 'issued' }}">
                                {{ $bill->status }}
                            </span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="details-table">
            <tr>
                <td style="width: 50%;">
                    <strong>Billed To:</strong><br>
                    {{ $bill->customer->name }}<br>
                    @if($bill->customer->organization)
                        {{ $bill->customer->organization->name }}<br>
                    @endif
                    {{ $bill->customer->phone }}<br>
                    {{ $bill->customer->email }}
                </td>
                <td style="width: 50%;" class="text-right">
                    @if($bill->booking)
                        <strong>Stay Details:</strong><br>
                        Room: {{ $bill->booking->room->room_number }} ({{ $bill->booking->room->roomType->name }})<br>
                        Check-in: {{ $bill->booking->check_in_date->format('M d, Y') }}<br>
                        Check-out: {{ $bill->booking->check_out_date->format('M d, Y') }}<br>
                        Duration: {{ $bill->booking->nights }} Night(s)
                    @endif
                </td>
            </tr>
        </table>

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