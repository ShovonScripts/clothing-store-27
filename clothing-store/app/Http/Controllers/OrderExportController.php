<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use League\Csv\Writer;

class OrderExportController extends Controller
{
    public function csv(Request $request)
    {
        $this->authorize('export_orders');

        $query = Order::query()
            ->with(['customer', 'items', 'payment'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('payment_method'), fn ($q) => $q->where('payment_method', $request->payment_method))
            ->when($request->filled('payment_status'), fn ($q) => $q->where('payment_status', $request->payment_status))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderBy('created_at', 'desc');

        $orders = $query->get();

        $csv = Writer::createFromFileObject(new \SplTempFileObject);
        $csv->insertOne([
            'Order Number',
            'Customer Name',
            'Customer Phone',
            'Customer Email',
            'Status',
            'Payment Method',
            'Payment Status',
            'Subtotal',
            'Delivery Charge',
            'Discount',
            'Total',
            'Shipping Name',
            'Shipping Phone',
            'Shipping Address',
            'Shipping District',
            'Shipping Area',
            'Coupon Code',
            'Discount Amount',
            'Order Date',
            'Payment TrxID',
            'Payment Sender',
            'Payment Amount',
            'Items (Product x Qty)',
        ]);

        foreach ($orders as $order) {
            $items = $order->items->map(fn ($item) => "{$item->product_name} (Size: {$item->variant_size}, Color: {$item->variant_color}) x {$item->qty}")->implode('; ');

            $csv->insertOne([
                $order->order_number,
                $order->shipping_name,
                $order->shipping_phone,
                $order->customer_email,
                $order->status->label(),
                $order->payment_method->label(),
                $order->payment_status->label(),
                $order->subtotal,
                $order->delivery_charge,
                $order->discount,
                $order->total,
                $order->shipping_name,
                $order->shipping_phone,
                $order->shipping_address.', '.($order->shippingArea?->name ?? '').', '.($order->shippingDistrict?->name ?? ''),
                $order->shippingDistrict?->name ?? '',
                $order->shippingArea?->name ?? '',
                $order->coupon?->code ?? '',
                $order->discount_amount,
                $order->created_at->format('Y-m-d H:i:s'),
                $order->bkash_trxid ?? '',
                $order->bkash_sender ?? '',
                $order->bkash_amount ?? '',
                $items,
            ]);
        }

        $filename = 'orders-export-'.now()->format('Y-m-d-H-i-s').'.csv';

        return Response::streamDownload(function () use ($csv) {
            $csv->output('php://output');
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
