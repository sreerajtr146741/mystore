<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
            text-align: left;
            background: #fff;
        }
        
        /* Header */
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .logo-section {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }
        .logo-section h1 {
            margin: 0;
            color: #333;
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .invoice-meta {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: middle;
        }
        .invoice-meta h2 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 24px;
        }
        
        /* Information Section */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 40px;
            border-top: 2px solid #eee;
            padding-top: 20px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-col h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
            text-transform: uppercase;
        }
        .info-col p {
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .items-table .item-name {
            color: #333;
            font-weight: 500;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .total-row td {
            border-bottom: none;
            font-weight: bold;
            color: #333;
            font-size: 16px;
            background-color: #fcfcfc;
            border-top: 2px solid #eee;
        }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        /* Status Badge */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            background: #e2e8f0;
            color: #4a5568;
        }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <div class="invoice-header">
            <div class="logo-section">
                <h1>MyStore</h1>
            </div>
            <div class="invoice-meta">
                <h2>INVOICE</h2>
                <p>
                    <strong>Invoice #:</strong> INV-{{ sprintf('%05d', $order->id) }}<br>
                    <strong>Date:</strong> {{ $order->created_at->format('M d, Y') }}<br>
                    <strong>Status:</strong> 
                    <span class="badge badge-{{ $order->payment_status === 'paid' ? 'paid' : 'pending' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Billing & Shipping Info -->
        <div class="info-section">
            <div class="info-col">
                <h3>Billed To:</h3>
                <p>
                    <strong>{{ $order->user->name ?? 'Guest User' }}</strong><br>
                    {{ $order->user->email ?? 'No Email' }}<br>
                    {{ $order->user->phone ?? 'No Phone' }}
                </p>
            </div>
            <div class="info-col" style="text-align: right;">
                <h3>Shipped To:</h3>
                <p>
                    <strong>{{ $order->shipping_address ?? 'Same as billing' }}</strong>
                </p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="50%">Item Description</th>
                    <th width="15%" class="text-right">Price</th>
                    <th width="15%" class="text-right">Qty</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td class="item-name">{{ $item->product->name ?? 'Product' }}</td>
                    <td class="text-right">${{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">${{ number_format($item->price * $item->qty, 2) }}</td>
                </tr>
                @endforeach
                
                <!-- Subtotal / Shipping / Discount Logic if available in Order model, using rough calcs for now -->
                <!-- Assuming 'total' column is the final pay amount -->
                
                @php
                    $subtotal = $order->total; // Simplified as per previous logic
                    // If you store subtotal/tax separately, adjust here. 
                    // Based on CheckoutController, total IS the final calculation.
                @endphp

                <tr class="total-row">
                    <td colspan="3" class="text-right">Grand Total</td>
                    <td class="text-right">${{ number_format($order->total, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>If you have any questions about this invoice, please contact support@mystore.com</p>
        </div>
    </div>
</body>
</html>
