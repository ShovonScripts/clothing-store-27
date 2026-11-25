<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Packing Slip') }} - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Noto Sans Bengali', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .slip-container {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .company-info h1 {
            margin: 0 0 10px;
            font-size: 24px;
            color: #333;
        }
        .company-info p {
            margin: 5px 0;
            color: #666;
        }
        .slip-details {
            text-align: right;
        }
        .slip-details h2 {
            margin: 0 0 15px;
            font-size: 20px;
            color: #333;
        }
        .slip-meta {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 8px 20px;
        }
        .slip-meta div {
            display: contents;
        }
        .slip-meta label {
            font-weight: bold;
            color: #666;
        }
        .slip-meta span {
            color: #333;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .addresses {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .address-block h4 {
            margin: 0 0 8px;
            font-size: 12px;
            color: #666;
        }
        .address-block p {
            margin: 3px 0;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
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
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .barcode {
            text-align: center;
            margin: 20px 0;
            font-family: 'Libre Barcode 128', monospace;
            font-size: 36px;
        }
        @media print {
            body {
                padding: 0;
            }
            .slip-container {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="slip-container">
        <div class="header">
            <div class="company-info">
                <h1>{{ config('store.name') }}</h1>
                <p>{{ config('store.address') }}</p>
                <p>{{ config('store.phone') }}</p>
            </div>
            <div class="slip-details">
                <h2>{{ __('Packing Slip') }}</h2>
                <div class="slip-meta">
                    <label>{{ __('Order #') }}</label>
                    <span>{{ $order->order_number }}</span>
                    <label>{{ __('Date') }}</label>
                    <span>{{ $order->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">{{ __('Shipping Address') }}</h3>
            <div class="addresses">
                <div class="address-block">
                    <h4>{{ __('Ship To') }}</h4>
                    <p>{{ $order->shipping_name }}</p>
                    <p>{{ $order->shipping_phone }}</p>
                    <p>{{ $order->shipping_address }}, {{ $order->shippingArea?->name }}, {{ $order->shippingDistrict?->name }}</p>
                </div>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">{{ __('Items to Pack') }}</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">{{ __('global.product') }}</th>
                        <th class="text-center" style="width: 15%;">{{ __('Size') }}</th>
                        <th class="text-center" style="width: 15%;">{{ __('Color') }}</th>
                        <th class="text-center" style="width: 10%;">{{ __('Qty') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-center">{{ $item->variant_size }}</td>
                            <td class="text-center">{{ $item->variant_color }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3 class="section-title">{{ __('Order Summary') }}</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <h4>{{ __('Order Details') }}</h4>
                    <p><strong>{{ __('Order #') }}:</strong> {{ $order->order_number }}</p>
                    <p><strong>{{ __('Date') }}:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                    <p><strong>{{ __('Payment') }}:</strong> {{ $order->payment_method->label() }}</p>
                </div>
                <div>
                    <h4>{{ __('Totals') }}</h4>
                    <p><strong>{{ __('Items') }}:</strong> {{ $order->items->sum('qty') }}</p>
                    <p><strong>{{ __('Total') }}:</strong> {{ number_format($order->total) }}</p>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>{{ config('store.name') }} - {{ config('store.phone') }} - {{ config('store.email') }}</p>
            <p>{{ __('This packing slip is for internal use only.') }}</p>
        </div>
    </div>
</body>
</html>