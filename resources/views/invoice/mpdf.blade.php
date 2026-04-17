<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .container { width: 100%; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .logo img { width: 80px; }
        .company-details { text-align: right; font-size: 10px; line-height: 1.2; }
        .invoice-info { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .invoice-info div { width: 48%; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .totals { float: right; width: 40%; margin-top: 10px; }
        .totals table { border: none; }
        .totals td { border: none; padding: 5px 10px; }
        .terms { margin-top: 50px; font-size: 10px; }
        .qr { text-align: center; margin-top: 20px; }
        .amount-due { font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('logo.png') }}" alt="Logo">
        </div>
        <div class="company-details">
            <strong>The Car Doctors Ltd.</strong><br>
            547 High Street, London<br>
            Co. Reg. No: 12345678<br>
            VAT No: GB123456789<br>
            Email: contact@thecardoctors.co.uk<br>
            Phone: 07831261234<br>
            Website: thecardoctors.co.uk
        </div>
    </div>

    <!-- Bill To & Invoice Info -->
    <div class="invoice-info">
        <div>
            <strong>Bill to:</strong><br>
            {{ $invoice->customer_name }}<br>
            {{ $invoice->vin_chassis_no }}<br>
            <!-- Add more customer details if needed -->
        </div>
        <div>
            <strong>Invoice:</strong><br>
            #{{ $invoice->id }}<br>
            Invoice Date: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') }}<br>
            Due Date: {{ \Carbon\Carbon::parse($invoice->created_at)->addDays(14)->format('d/m/Y') }}
        </div>
    </div>

    <!-- Invoice Table -->
    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Price</th>
            <th>VAT</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Car Service</td>
            <td>1</td>
            <td>each</td>
            <td>{{ number_format($invoice->price,2) }}</td>
            <td>{{ number_format($invoice->tax,2) }}</td>
            <td>{{ number_format($invoice->net_amount,2) }}</td>
        </tr>
        </tbody>
    </table>

    <!-- Totals -->
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td>{{ number_format($invoice->price,2) }}</td>
            </tr>
            <tr>
                <td>VAT:</td>
                <td>{{ number_format($invoice->tax,2) }}</td>
            </tr>
            <tr class="amount-due">
                <td>Total:</td>
                <td>{{ number_format($invoice->net_amount,2) }}</td>
            </tr>
            <tr>
                <td>Amount Paid:</td>
                <td>0.00</td>
            </tr>
            <tr class="amount-due">
                <td>Amount Due:</td>
                <td>{{ number_format($invoice->net_amount,2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Terms -->
    <div class="terms">
        <strong>Terms & Conditions:</strong><br>
      <!--  Payment to be made within 14 days via the payment link below.-->
    </div>

    <!-- QR -->
    <div class="qr">
        <img src="{{ public_path($qrPath) }}" width="120" alt="QR Code"><br>
        Scan to open
    </div>
</div>
</body>
</html>
