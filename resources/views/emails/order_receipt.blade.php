<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Your MyStore Order Receipt</title>
  <style>
    body { font-family: Arial, sans-serif; color:#222; }
    .box { max-width:600px; margin:0 auto; border:1px solid #eee; padding:20px; border-radius:10px; }
    h1 { font-size:20px; margin-bottom:10px; }
    table { width:100%; border-collapse:collapse; margin-top:10px; }
    th, td { padding:8px; border-bottom:1px solid #eee; text-align:left; }
    .right { text-align:right; }
    .totals td { font-weight:bold; }
  </style>
</head>
<body>
  <div class="box">
    <h1>Thanks for your order, {{ $buyer['full_name'] ?? 'Customer' }}!</h1>

    <p><strong>Delivery to:</strong><br>
      {{ $buyer['full_name'] ?? '' }}<br>
      {{ $buyer['address'] ?? '' }}<br>
      {{ $buyer['phone'] ?? '' }}<br>
      {{ $buyer['email'] ?? '' }}
    </p>

    <h2 style="font-size:16px;margin-top:20px;">Items</h2>
    <table>
      <thead>
        <tr>
          <th>Item</th>
          <th class="right">Qty</th>
          <th class="right">Price</th>
          <th class="right">Line Total</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($items as $it)
          <tr>
            <td>{{ $it['name'] ?? 'Item' }}</td>
            <td class="right">{{ (int)($it['qty'] ?? 1) }}</td>
            <td class="right">₹{{ number_format((float)($it['price'] ?? 0), 2) }}</td>
            <td class="right">₹{{ number_format((float)($it['line_total'] ?? ((float)($it['price'] ?? 0) * (int)($it['qty'] ?? 1))), 2) }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="totals">
          <td colspan="3" class="right">Subtotal</td>
          <td class="right">₹{{ number_format((float)$subtotal, 2) }}</td>
        </tr>
        <tr class="totals">
          <td colspan="3" class="right">Shipping</td>
          <td class="right">₹{{ number_format((float)$shipping, 2) }}</td>
        </tr>
        <tr class="totals">
          <td colspan="3" class="right">Total</td>
          <td class="right">₹{{ number_format((float)$total, 2) }}</td>
        </tr>
      </tfoot>
    </table>

    <p style="margin-top:20px;">If you have any questions, just reply to this email.</p>
    <p>— MyStore</p>
  </div>
</body>
</html>
