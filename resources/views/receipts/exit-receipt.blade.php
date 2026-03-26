<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exit Receipt - Ticket #{{ $ticket->getId() }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .receipt-container {
            max-width: 400px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10px;
            color: #666;
        }
        .receipt-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .ticket-number {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
            padding: 10px;
            background: #f0f0f0;
            border: 1px solid #000;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 3px 0;
        }
        .info-label {
            font-weight: bold;
            width: 45%;
        }
        .info-value {
            width: 55%;
            text-align: right;
        }
        .total-section {
            margin-top: 20px;
            padding: 15px;
            background: #f0f0f0;
            border: 2px solid #000;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .total-row.final {
            font-size: 18px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 10px;
        }
        .payment-info {
            background: #d4edda;
            border: 1px solid #28a745;
            padding: 10px;
            margin-top: 15px;
            text-align: center;
            font-size: 11px;
        }
        .qr-placeholder {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            border: 1px dashed #ccc;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1>{{ $parkingLot->getName() }}</h1>
            <p>{{ $parkingLot->getAddress() }}</p>
        </div>

        <div class="receipt-title">Exit Receipt</div>

        <div class="ticket-number">
            Ticket #{{ $ticket->getId() }}
        </div>

        <div class="section">
            <div class="section-title">Vehicle Information</div>
            <div class="info-row">
                <span class="info-label">License plate:</span>
                <span class="info-value">{{ $vehicle->getPlate() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Owner:</span>
                <span class="info-value">{{ $vehicle->getOwnerName() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Type:</span>
                <span class="info-value">{{ ucfirst($vehicle->getVehicleType()) }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Parking Information</div>
            <div class="info-row">
                <span class="info-label">Parking lot:</span>
                <span class="info-value">{{ $parkingLot->getName() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Spot:</span>
                <span class="info-value">{{ $parkingSpot ? $parkingSpot->getSpotNumber() : 'N/A' }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Parking Duration</div>
            <div class="info-row">
                <span class="info-label">Entry time:</span>
                <span class="info-value">{{ $entryTime }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Exit time:</span>
                <span class="info-value">{{ $exitTime }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total time:</span>
                <span class="info-value">{{ number_format($totalHours, 2) }} hours</span>
            </div>
        </div>

        <div class="total-section">
            <div class="section-title" style="border: none; padding: 0; margin-bottom: 10px;">Charges</div>
            <div class="total-row">
                <span>Hourly rate:</span>
                <span>${{ number_format($hourlyRateApplied, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Hours parked:</span>
                <span>{{ number_format($totalHours, 2) }}</span>
            </div>
            <div class="total-row final">
                <span>TOTAL DUE:</span>
                <span>${{ number_format($totalAmount, 2) }}</span>
            </div>
        </div>

        @if($isPaid && $paymentMethod)
        <div class="payment-info">
            <strong>PAID</strong><br>
            Method: {{ ucfirst($paymentMethod) }}<br>
            @if($paymentTime)
            Date: {{ $paymentTime }}
            @endif
        </div>
        @endif

        <div class="qr-placeholder">
            [Ticket QR code]
        </div>

        <div class="footer">
            <p>Thank you for using our services</p>
            <p>Generated on {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
