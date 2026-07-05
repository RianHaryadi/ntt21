<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Digital Ticket - Pesona NTT</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet" />
  <style>
    :root {
      --petrol: #001a33;
      --laut: #0F6E63;
      --white: #ffffff;
      --gray-50: #f8fafc;
      --gray-100: #f1f5f9;
      --gray-400: #94a3b8;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: var(--gray-100);
      margin: 0;
      padding: 40px 20px;
      color: var(--petrol);
    }

    .email-container {
      max-width: 600px;
      margin: 0 auto;
      background: var(--white);
      border-radius: 32px;
      overflow: hidden;
      box-shadow: 0 20px 50px rgba(0, 26, 51, 0.1);
    }

    .header {
      background-color: var(--petrol);
      padding: 48px;
      text-align: center;
      position: relative;
    }

    .logo {
      font-family: 'Montserrat', sans-serif;
      font-weight: 900;
      font-size: 24px;
      color: var(--white);
      text-decoration: none;
      letter-spacing: -1px;
    }

    .logo span {
      color: var(--laut);
    }

    .success-icon {
      width: 64px;
      height: 64px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      margin: 32px auto 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      color: var(--laut);
    }

    .headline {
      font-family: 'Montserrat', sans-serif;
      font-weight: 900;
      font-size: 32px;
      color: var(--white);
      margin: 0 0 8px;
    }

    .sub-headline {
      color: rgba(255, 255, 255, 0.6);
      font-size: 14px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .content {
      padding: 48px;
    }

    .greeting {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 16px;
    }

    .booking-ref {
      background: var(--gray-50);
      border: 2px dashed var(--gray-100);
      border-radius: 20px;
      padding: 24px;
      text-align: center;
      margin-bottom: 32px;
    }

    .ref-label {
      font-size: 10px;
      font-weight: 900;
      color: var(--gray-400);
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 8px;
    }

    .ref-code {
      font-family: monospace;
      font-size: 24px;
      font-weight: 900;
      color: var(--petrol);
      letter-spacing: 4px;
    }

    .section-title {
      font-family: 'Montserrat', sans-serif;
      font-weight: 800;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: var(--petrol);
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .info-card {
      border: 1px solid var(--gray-100);
      border-radius: 24px;
      padding: 24px;
      margin-bottom: 32px;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 16px;
    }

    .info-row:last-child {
      margin-bottom: 0;
    }

    .label {
      font-size: 12px;
      font-weight: 700;
      color: var(--gray-400);
    }

    .value {
      font-size: 13px;
      font-weight: 800;
      color: var(--petrol);
      text-align: right;
    }

    .total-row {
      margin-top: 16px;
      padding-top: 16px;
      border-top: 1px solid var(--gray-100);
    }

    .total-value {
      color: var(--laut);
      font-size: 18px;
    }

    .qr-section {
      text-align: center;
      margin-bottom: 48px;
    }

    .qr-image {
      background: white;
      padding: 16px;
      border: 1px solid var(--gray-100);
      border-radius: 24px;
      width: 150px;
      height: 150px;
    }

    .footer {
      text-align: center;
      background: var(--gray-50);
      padding: 48px;
      color: var(--gray-400);
      font-size: 12px;
      font-weight: 600;
    }

    .footer p {
      margin: 4px 0;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <div class="logo">Pesona<span>NTT</span></div>
      <div class="success-icon">✓</div>
      <h1 class="headline">Payment Verified</h1>
      <p class="sub-headline">Your journey begins here</p>
    </div>

    <div class="content">
      <p class="greeting">Hi {{ $transaction->customer_name }},</p>
      <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 32px;">
        Your reservation has been confirmed. We're excited to host you in East Nusa Tenggara. Below are your digital tickets and booking summary.
      </p>

      <div class="booking-ref">
        <div class="ref-label">Booking Reference</div>
        <div class="ref-code">{{ $transaction->booking_code }}</div>
      </div>

      <div class="qr-section">
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($transaction->booking_code) }}" class="qr-image" alt="QR Code" />
        <p style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-top: 16px;">Scan on Arrival</p>
      </div>

      <div class="section-title">Adventure Details</div>
      <div class="info-card">
        <div class="info-row">
          <div class="label">Destination</div>
          <div class="value">{{ $transaction->destination?->name ?? $transaction->tourPackage?->name }}</div>
        </div>
        <div class="info-row">
          <div class="label">Booking Date</div>
          <div class="value">{{ \Carbon\Carbon::parse($transaction->booking_date)->format('d F Y') }}</div>
        </div>
        <div class="info-row">
          <div class="label">Customer</div>
          <div class="value">{{ $transaction->customer_name }}</div>
        </div>
      </div>

      <div class="section-title">Payment Summary</div>
      <div class="info-card">
        <div class="info-row">
          <div class="label">Amount for {{ $transaction->number_of_tickets }} Tickets</div>
          <div class="value">Rp {{ number_format($transaction->package_price * $transaction->number_of_tickets, 0, ',', '.') }}</div>
        </div>
        @if($transaction->discount_amount > 0)
        <div class="info-row">
          <div class="label">Discount Applied</div>
          <div class="value" style="color: #ef4444;">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</div>
        </div>
        @endif
        <div class="info-row total-row">
          <div class="label" style="color: var(--petrol); font-weight: 800;">TOTAL PAID</div>
          <div class="value total-value">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</div>
        </div>
      </div>
    </div>

    <div class="footer">
      <p>&copy; {{ date('Y') }} Pesona NTT• OFFICIAL JOURNEY DOCUMENT</p>
      <p>Kupang, East Nusa Tenggara, Indonesia</p>
      <p style="margin-top: 16px; color: var(--petrol);">Powered by Pesona NTTTravel Desk</p>
    </div>
  </div>
</body>
</html>