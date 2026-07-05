<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Ticket {{ $transaction->booking_code }}</title>
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
            color: #D2674A;
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
            color: #D2674A;
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
        .ticket-card {
            border: 1px solid #DCDED5;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 10px;
        }
        .ticket-code {
            font-family: 'Courier New', monospace;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 2px;
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
            color: #D2674A;
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
            background-color: #D2674A;
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
        <div class="tagline">Official Digital E-Ticket</div>
        <div class="ref-box">
            <div class="ref-label">Booking Reference</div>
            <div class="ref-number">{{ $transaction->booking_code }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Booking Details</div>
        <table class="info">
            <tr>
                <td>
                    <div class="label">Guest Name</div>
                    <div class="value">{{ $transaction->customer_name }}</div>
                </td>
                <td>
                    <div class="label">Status</div>
                    <div class="value"><span class="badge">{{ strtoupper($transaction->status) }}</span></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">Email</div>
                    <div class="value">{{ $transaction->customer_email }}</div>
                </td>
                <td>
                    <div class="label">Phone</div>
                    <div class="value">{{ $transaction->customer_phone }}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="label">Destination / Package</div>
                    <div class="value">{{ $transaction->destination?->name ?? $transaction->tourPackage?->name ?? '-' }}</div>
                </td>
                <td>
                    <div class="label">Visit Date</div>
                    <div class="value">{{ $transaction->booking_date?->translatedFormat('d F Y') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Tickets ({{ $transaction->tickets->count() }})</div>
        @foreach($transaction->tickets as $ticket)
        <div class="ticket-card">
            <div class="label">Ticket Code</div>
            <div class="ticket-code">{{ $ticket->ticket_code }}</div>
        </div>
        @endforeach
    </div>

    <div class="section">
        <div class="section-title">Price Summary</div>
        <table class="price">
            <tr>
                <td>Number of Tickets</td>
                <td class="amount">{{ $transaction->number_of_tickets }}</td>
            </tr>
            <tr>
                <td>Package Price</td>
                <td class="amount">Rp{{ number_format($transaction->package_price, 0, ',', '.') }}</td>
            </tr>
            @if($transaction->discount_amount)
            <tr>
                <td>Discount</td>
                <td class="amount">-Rp{{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($transaction->has_insurance)
            <tr>
                <td>Travel Insurance</td>
                <td class="amount">Rp{{ number_format($transaction->insurance_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total Paid</td>
                <td class="amount">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Please present this e-ticket and a valid ID at the entrance.<br>
        Generated on {{ now()->format('d M Y H:i') }} &middot; Pesona NTT &middot; support@pesonantt.com
    </div>
</body>
</html>
