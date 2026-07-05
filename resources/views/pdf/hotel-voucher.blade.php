<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Voucher {{ $booking->booking_number }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #16201E;
            margin: 0;
            padding: 40px;
            font-size: 12px;
        }
        .header {
            background-color: #16201E;
            color: #F7F6F2;
            padding: 24px 32px;
            border-radius: 8px;
        }
        .brand {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .tagline {
            font-size: 10px;
            color: #DCDED5;
            margin-top: 2px;
        }
        .ref-box {
            margin-top: 18px;
            border-top: 1px solid rgba(247,246,242,0.2);
            padding-top: 12px;
        }
        .ref-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0F6E63;
            font-weight: bold;
        }
        .ref-number {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
        }
        .section {
            margin-top: 24px;
        }
        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0F6E63;
            font-weight: bold;
            border-bottom: 1px solid #DCDED5;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        table.info {
            width: 100%;
            border-collapse: collapse;
        }
        table.info td {
            padding: 6px 0;
            vertical-align: top;
            width: 50%;
        }
        .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #69736E;
        }
        .value {
            font-size: 13px;
            font-weight: bold;
            color: #16201E;
        }
        table.price {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.price td {
            padding: 6px 0;
            font-size: 12px;
        }
        table.price .amount {
            text-align: right;
        }
        .total-row td {
            border-top: 1px solid #DCDED5;
            padding-top: 12px;
            font-size: 15px;
            font-weight: bold;
        }
        .total-row .amount {
            color: #0F6E63;
        }
        .footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px dashed #DCDED5;
            font-size: 9px;
            color: #69736E;
            text-align: center;
        }
        .badge {
            display: inline-block;
            background-color: #0F6E63;
            color: #F7F6F2;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 10px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">PESONA NTT</div>
        <div class="tagline">Official Hotel Booking Voucher</div>
        <div class="ref-box">
            <div class="ref-label">Booking Reference</div>
            <div class="ref-number">{{ $booking->booking_number }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Guest &amp; Stay Details</div>
        <table class="info">
            <tr>
                <td>
                    <div class="label">Guest Name</div>
                    <div class="value">{{ $booking->customer_name }}</div>
                </td>
                <td>
                    <div class="label">Status</div>
                    <div class="value"><span class="badge">{{ strtoupper($booking->status) }}</span></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">Email</div>
                    <div class="value">{{ $booking->customer_email }}</div>
                </td>
                <td>
                    <div class="label">Phone</div>
                    <div class="value">{{ $booking->customer_phone }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Hotel &amp; Room</div>
        <table class="info">
            <tr>
                <td>
                    <div class="label">Hotel</div>
                    <div class="value">{{ $booking->hotel->name ?? '-' }}</div>
                </td>
                <td>
                    <div class="label">Room Type</div>
                    <div class="value">{{ ucfirst($booking->room_type) }} Room</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">Location</div>
                    <div class="value">{{ $booking->hotel->location ?? '-' }}</div>
                </td>
                <td>
                    <div class="label">Nights</div>
                    <div class="value">{{ $booking->night_count }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">Check-In</div>
                    <div class="value">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</div>
                </td>
                <td>
                    <div class="label">Check-Out</div>
                    <div class="value">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Price Breakdown</div>
        <table class="price">
            <tr>
                <td>Base Rate ({{ $booking->night_count }} nights)</td>
                <td class="amount">Rp{{ number_format($booking->room_price * $booking->night_count, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tax</td>
                <td class="amount">Rp{{ number_format($booking->tax, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Service Charge</td>
                <td class="amount">Rp{{ number_format($booking->service_charge, 0, ',', '.') }}</td>
            </tr>
            @if($booking->promo_code)
            <tr>
                <td>Voucher ({{ $booking->promo_code }})</td>
                <td class="amount">-Rp{{ number_format($booking->discount_amount ?? 0, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($booking->has_insurance)
            <tr>
                <td>Travel Insurance</td>
                <td class="amount">Rp{{ number_format($booking->insurance_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Paid</td>
                <td class="amount">Rp{{ number_format($booking->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Please present this voucher and a valid ID at the hotel reception during check-in.<br>
        Generated on {{ now()->format('d M Y H:i') }} &middot; Pesona NTT &middot; support@pesonantt.com
    </div>
</body>
</html>
