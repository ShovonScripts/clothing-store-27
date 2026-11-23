<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Invoice') }} - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Noto Sans Bengali', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .company-info h1 {
            margin: 0 0 10px;
            font-size: 28px;
            color: #333;
        }
        .company-info p {
            margin: 5px 0;
            color: #666;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            margin: 0 0 15px;
            font-size: 24px;
            color: #333;
        }
        .invoice-meta {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 8px 20px;
        }
        .invoice-meta div {
            display: contents;
        }
        .invoice-meta label {
            font-weight: bold;
            color: #666;
        }
        .invoice-meta span {
            color: #333;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ddd;
        }
        .addresses {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .address-block h4 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #666;
        }
        .address-block p {
            margin: 5px 0;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
        }
        td {
            color: #333;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-left: auto;
            width: 300px;
        }
        .totals table {
            width: 100%;
        }
        .totals th {
            text-align: left;
        }
        .totals td {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 11px;
        }
        .payment-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .payment-info h4 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #333;
        }
        .payment-info p {
            margin: 5px 0;
        }
        @media print {
            body {
                padding: 0;
            }
            .invoice-container {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <h1>{{ config('store.name') }}</h1>
                <p>{{ config('store.address') }}</p>
                <p>{{ config('store.phone') }}</p>
                <p>{{ config('store.email') }}</p>
            </div>
            <div class="invoice-details">
                <h2>{{ __('Invoice') }}</h2>
                <div class="invoice-meta">
                    <label>{{ __('Invoice #') }}</label>
                    <span>{{ $order->order_number }}</span>
                    <label>{{ __('Date') }}</label>
                    <span>{{ $order->created_at->format('M d, Y') }}</span>
                    <label>{{ __('Status') }}</label>
                    <span>{{ $order->status->label() }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">{{ __('Billing & Shipping Information') }}</h3>
            <div class="addresses">
                <div class="address-block">
                    <h4>{{ __('Bill To') }}</h4>
                    <p>{{ $order->shipping_name }}</p>
                    <p>{{ $order->shipping_phone }}</p>
                    <p>{{ $order->shipping_address }}, {{ $order->shippingArea?->name }}, {{ $order->shippingDistrict?->name }}</p>
                    @if($order->customer_email)
                        <p>{{ $order->customer_email }}</p>
                    @endif
                </div>
                <div class="address-block">
                    <h4>{{ __('Ship To') }}</h4>
                    <p>{{ $order->shipping_name }}</p>
                    <p>{{ $order->shipping_phone }}</p>
                    <p>{{ $order->shipping_address }}, {{ $order->shippingArea?->name }}, {{ $order->shippingDistrict?->name }}</p>
                </div>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">{{ __('Order Items') }}</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 40%;">{{ __('Product') }}</th>
                        <th class="text-center" style="width: 10%;">{{ __('Size') }}</th>
                        <th class="text-center" style="width: 10%;">{{ __('Color') }}</th>
                        <th class="text-center" style="width: 10%;">{{ __('Qty') }}</th>
                        <th class="text-right" style="width: 15%;">{{ __('Unit Price') }}</th>
                        <th class="text-right" style="width: 15%;">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-center">{{ $item->variant_size }}</td>
                            <td class="text-center">{{ $item->variant_color }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-right">{{ number_format($item->price) }}</td>
                            <td class="text-right">{{ number_format($item->line_total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="totals">
                <table>
                    <tr>
                        <th>{{ __('Subtotal') }}</th>
                        <td>{{ number_format($order->subtotal) }}</td>
                    </tr>
                    @if ($order->discount > 0)
                        <tr>
                            <th>{{ __('Discount') }}</th>
                            <td style="color: green;">-{{ number_format($order->discount) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>{{ __('Delivery') }}</th>
                        <td>{{ number_format($order->delivery_charge) }}</td>
                    </tr>
                    <tr class="total-row">
                        <th>{{ __('Total') }}</th>
                        <td>{{ number_format($order->total) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if ($order->isBkash())
        <div class="section payment-info">
            <h4>{{ __('Payment Information') }}</h4>
            <p><strong>{{ __('Method') }}:</strong> {{ $order->payment_method->label() }}</p>
            <p><strong>{{ __('TrxID') }}:</strong> {{ $order->bkash_trxid }}</p>
            <p><strong>{{ __('Sender') }}:</strong> {{ $order->bkash_sender }}</p>
            <p><strong>{{ __('Amount') }}:</strong> {{ number_format($order->bkash_amount) }}</p>
            <p><strong>{{ __('Status') }}:</strong> {{ $order->payment_status->label() }}</p>
        </div>
        @endif

        <div class="footer">
            <p>{{ __('Thank you for your order!') }}</p>
            <p>{{ config('store.name') }} - {{ config('store.phone') }} - {{ config('store.email') }}</p>
            <p>{{ __('This is a computer-generated invoice. No signature required.') }}</p>
        </div>
    </div>
</body>
</html>