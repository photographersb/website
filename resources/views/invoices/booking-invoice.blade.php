<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 900px; margin: 0 auto; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; border-bottom: 3px solid #8B1538; padding-bottom: 20px; }
        .company-info h1 { color: #8B1538; font-size: 32px; margin-bottom: 5px; }
        .company-info p { color: #666; font-size: 12px; margin: 2px 0; }
        .invoice-details { text-align: right; }
        .invoice-details .label { color: #666; font-size: 12px; font-weight: bold; }
        .invoice-details .value { color: #333; font-size: 14px; margin-bottom: 8px; }
        
        .parties { display: flex; gap: 40px; margin-bottom: 40px; }
        .party { flex: 1; }
        .party h3 { color: #8B1538; font-size: 12px; font-weight: bold; margin-bottom: 8px; text-transform: uppercase; }
        .party p { font-size: 13px; color: #333; margin: 3px 0; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        table.items thead { background-color: #8B1538; color: white; }
        table.items th { padding: 12px; text-align: left; font-size: 12px; font-weight: bold; }
        table.items td { padding: 12px; border-bottom: 1px solid #eee; font-size: 13px; }
        table.items tbody tr:last-child td { border-bottom: 2px solid #8B1538; }
        
        .totals { display: flex; justify-content: flex-end; margin-bottom: 40px; }
        .totals-table { width: 350px; }
        .totals-table .row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; }
        .totals-table .row.total { background-color: #8B1538; color: white; padding: 12px 8px; font-weight: bold; font-size: 16px; margin-top: 10px; }
        .totals-table .label { color: #666; }
        .totals-table .amount { font-weight: bold; color: #333; }
        .totals-table .row.total .amount { color: white; }
        
        .terms { background-color: #f9f9f9; padding: 20px; border-radius: 5px; font-size: 11px; color: #666; }
        .terms h4 { color: #8B1538; font-size: 12px; font-weight: bold; margin-bottom: 8px; }
        .terms p { margin-bottom: 5px; }
        
        .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 20px; text-align: center; font-size: 11px; color: #999; }
        
        @media print {
            body { margin: 0; padding: 0; }
            .container { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <h1>📸 {{ $companyName }}</h1>
                <p>{{ $companyAddress }}</p>
                <p>Phone: {{ $companyPhone }}</p>
                <p>Email: {{ $companyEmail }}</p>
            </div>
            <div class="invoice-details">
                <div class="label">INVOICE</div>
                <div class="value" style="font-size: 18px; font-weight: bold; color: #8B1538;">{{ $invoiceNumber }}</div>
                <div class="label">Invoice Date</div>
                <div class="value">{{ $invoiceDate }}</div>
                <div class="label">Due Date</div>
                <div class="value">{{ $dueDate }}</div>
            </div>
        </div>

        <!-- Parties -->
        <div class="parties">
            <div class="party">
                <h3>Bill To</h3>
                <p><strong>{{ $booking->client->name }}</strong></p>
                <p>{{ $booking->client->email }}</p>
                <p>{{ $booking->client->phone ?? 'Phone not provided' }}</p>
            </div>
            <div class="party">
                <h3>Service Provider</h3>
                <p><strong>{{ $booking->photographer->user->name }}</strong></p>
                <p>{{ $booking->photographer->user->email }}</p>
                <p>{{ $booking->photographer->user->phone ?? 'Phone not provided' }}</p>
            </div>
        </div>

        <!-- Service Details -->
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 20%; text-align: right;">Quantity</th>
                    <th style="width: 15%; text-align: right;">Unit Price</th>
                    <th style="width: 15%; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Photography Service</strong><br>
                        <small>Event Date: {{ $booking->event_date->format('M d, Y') }}</small><br>
                        @if($booking->inquiry?->description)
                            <small>{{ Str::limit($booking->inquiry->description, 80) }}</small>
                        @endif
                    </td>
                    <td style="text-align: right;">1</td>
                    <td style="text-align: right;">৳ {{ number_format($booking->total_amount, 2) }}</td>
                    <td style="text-align: right;">৳ {{ number_format($booking->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        @php
            $totals = [
                'subtotal' => $booking->total_amount,
                'tax' => round($booking->total_amount * 0.15, 2),
                'total' => $booking->total_amount + round($booking->total_amount * 0.15, 2),
            ];
        @endphp
        <div class="totals">
            <div class="totals-table">
                <div class="row">
                    <span class="label">Subtotal:</span>
                    <span class="amount">৳ {{ number_format($totals['subtotal'], 2) }}</span>
                </div>
                <div class="row">
                    <span class="label">VAT (15%):</span>
                    <span class="amount">৳ {{ number_format($totals['tax'], 2) }}</span>
                </div>
                <div class="row total">
                    <span>TOTAL DUE:</span>
                    <span>৳ {{ number_format($totals['total'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Terms & Notes -->
        <div class="terms">
            <h4>Terms & Conditions</h4>
            <p>1. Payment must be received by the due date to secure the booking.</p>
            <p>2. Cancellations made 7+ days before the event are eligible for a 50% refund.</p>
            <p>3. Cancellations made less than 7 days before the event are non-refundable.</p>
            <p>4. All deliverables will be provided within 30 days of the event.</p>
            <p>5. This invoice is valid for 30 days from the invoice date.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for choosing {{ $companyName }}! For any questions, please contact us at {{ $companyEmail }}</p>
            <p>Booking Reference: {{ $booking->uuid }} | Invoice Reference: {{ $invoiceNumber }}</p>
            <p style="margin-top: 10px;">Generated on {{ now()->format('M d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>
