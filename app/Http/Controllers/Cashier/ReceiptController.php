<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReceiptController extends Controller
{
    public function pdf(Order $order): Response
    {
        $order->load(['items', 'table', 'payment', 'creator']);

        $pdf = Pdf::loadView('receipt.thermal', compact('order'))
            ->setPaper([0, 0, 226.77, 800], 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $pdf->download("receipt-{$order->order_number}.pdf");
    }

    public function html(Order $order): \Illuminate\Http\Response
    {
        $order->load(['items', 'table', 'payment', 'creator']);

        return response()->view('receipt.thermal', compact('order'));
    }
}
