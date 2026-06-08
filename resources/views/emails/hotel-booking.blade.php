<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Booking Confirmation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet" />
    <style>
        :root {
            --ocean-900: #001a33;
            --sunset-500: #ff6b35;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-400: #94a3b8;
        }
        body {
            margin: 0; padding: 0;
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 26, 51, 0.1);
        }
        .header {
            background-color: #001a33;
            padding: 48px;
            text-align: center;
            color: white;
        }
        .logo {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 24px;
            letter-spacing: -1px;
            margin-bottom: 32px;
        }
        .logo span { color: #ff6b35; }
        .success-circle {
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            margin: 0 auto 24px;
            line-height: 64px;
            font-size: 32px;
            color: #ff6b35;
        }
        .content { padding: 48px; }
        .booking-card {
            background: #f8fafc;
            border-radius: 24px;
            padding: 24px;
            margin-bottom: 32px;
            border: 1px solid #f1f5f9;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 13px;
        }
        .label { color: #94a3b8; font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; }
        .value { color: #001a33; font-weight: 800; }
        .divider { border-top: 1px solid #f1f5f9; margin: 16px 0; padding-top: 16px; }
        .btn {
            display: inline-block;
            background: #ff6b35;
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.2);
        }
        .footer {
            background: #f8fafc;
            padding: 32px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Wonderful<span>NTT</span></div>
            <div class="success-circle">✓</div>
            <h1 style="font-family: Montserrat; font-weight: 900; margin: 0; font-size: 28px;">Stay Confirmed</h1>
            <p style="opacity: 0.6; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-top: 8px;">Reservation #{{ $booking->booking_number }}</p>
        </div>

        <div class="content">
            <p style="font-weight: 800; font-size: 18px; margin-bottom: 16px;">Hi {{ $booking->customer_name }},</p>
            <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 32px;">Your reservation at <strong>{{ $booking->hotel->name }}</strong> is officially on the books. We're looking forward to your arrival.</p>

            <div class="booking-card">
                <div class="detail-row">
                    <span class="label">Ref Number</span>
                    <span class="value" style="color: #ff6b35;">{{ $booking->booking_number }}</span>
                </div>
                <div class="divider"></div>
                <div class="detail-row">
                    <span class="label">Check In</span>
                    <span class="value">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d M Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Check Out</span>
                    <span class="value">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d M Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Room Category</span>
                    <span class="value">{{ ucfirst($booking->room_type) }}</span>
                </div>
            </div>

            <h3 style="font-family: Montserrat; font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px;">Financial Breakdown</h3>
            <div class="booking-card">
                <div class="detail-row">
                    <span class="label">Subtotal ({{ $booking->night_count }} Nights)</span>
                    <span class="value">Rp {{ number_format($booking->room_price * $booking->night_count, 0, ',', '.') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Taxes & Fees</span>
                    <span class="value">Rp {{ number_format($booking->tax + $booking->service_charge, 0, ',', '.') }}</span>
                </div>
                @if($booking->discount_amount > 0)
                <div class="detail-row">
                    <span class="label">Promotional Credit</span>
                    <span class="value" style="color: #ef4444;">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="divider"></div>
                <div class="detail-row" style="font-size: 16px;">
                    <span class="label" style="color: #001a33; font-weight: 800;">Total Charged</span>
                    <span class="value" style="color: #ff6b35;">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="{{ route('booking.checkForm') }}" class="btn">Manage My Stay</a>
                <p style="margin-top: 24px; font-size: 12px; color: #94a3b8; font-weight: 600;">Please show this confirmation at the front desk upon arrival.</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} WONDERFUL NTT • HOSPITALITY DIVISION</p>
            <p>Managed for Luxury and Comfort</p>
        </div>
    </div>
</body>
</html>