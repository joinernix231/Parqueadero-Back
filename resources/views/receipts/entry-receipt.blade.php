<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Entrada - Ticket #{{ $ticket->getId() }}</title>
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
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 10px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
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

        <div class="receipt-title">Recibo de Entrada</div>

        <div class="ticket-number">
            Ticket #{{ $ticket->getId() }}
        </div>

        <div class="section">
            <div class="section-title">Información del Vehículo</div>
            <div class="info-row">
                <span class="info-label">Placa:</span>
                <span class="info-value">{{ $vehicle->getPlate() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Propietario:</span>
                <span class="info-value">{{ $vehicle->getOwnerName() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo:</span>
                <span class="info-value">{{ ucfirst($vehicle->getVehicleType()) }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Información del Estacionamiento</div>
            <div class="info-row">
                <span class="info-label">Estacionamiento:</span>
                <span class="info-value">{{ $parkingLot->getName() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Espacio:</span>
                <span class="info-value">{{ $parkingSpot ? $parkingSpot->getSpotNumber() : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tipo de Espacio:</span>
                <span class="info-value">{{ $parkingSpot ? ucfirst($parkingSpot->getSpotType()) : 'N/A' }}</span>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Información de Entrada</div>
            <div class="info-row">
                <span class="info-label">Fecha y Hora:</span>
                <span class="info-value">{{ $entryTime }}</span>
            </div>
        </div>

        <div class="qr-placeholder">
            [Código QR del Ticket]
        </div>

        <div class="warning">
            <strong>IMPORTANTE:</strong> Conserve este recibo para su salida. El ticket será requerido al momento de retirar su vehículo.
        </div>

        <div class="footer">
            <p>Gracias por utilizar nuestros servicios</p>
            <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>

