<!DOCTYPE html>
<html>
<head>
    <title>Invoice - {{ $sale->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #006D77; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #006D77; margin: 0; }
        .info { display: flex; justify-content: space-between; margin-bottom: 20px; }
        table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        table th { background: #f2f2f2; padding: 10px; border: 1px solid #ddd; }
        table td { padding: 10px; border: 1px solid #ddd; }
        .total { text-align: right; margin-top: 20px; }
        .total h3 { color: #006D77; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>ZWETOE PHARMACY</h1>
                <p>Health & Wellness in Every Dose</p>
            </div>
            <div style="text-align: right;">
                <h2>INVOICE</h2>
                <p>#{{ $sale->invoice_number }}</p>
            </div>
        </div>

        <div class="info">
            <div>
                <strong>Customer:</strong><br>
                {{ $sale->customer_name ?: ($sale->customer->name ?? 'Walk-in Customer') }}
            </div>
            <div style="text-align: right;">
                <strong>Date:</strong> {{ $sale->created_at->format('M d, Y H:i') }}<br>
                <strong>Payment:</strong> {{ strtoupper($sale->payment_method) }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->medicine->name }}</td>
                    <td>{{ number_format($item->unit_price) }} MMK</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->subtotal) }} MMK</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            <p>Total Amount: {{ number_format($sale->total_amount) }} MMK</p>
            <p>Discount: {{ number_format($sale->discount) }} MMK</p>
            <hr>
            <h3>Payable Amount: {{ number_format($sale->payable_amount) }} MMK</h3>
        </div>

        <div class="footer">
            <p>Thank you for choosing Zwetoe Pharmacy!</p>
            <p>Please keep this invoice for your records.</p>
            <button class="no-print" onclick="window.print()" style="margin-top: 20px; padding: 10px 20px; background: #006D77; color: white; border: none; border-radius: 5px; cursor: pointer;">Print Again</button>
        </div>
    </div>
</body>
</html>
