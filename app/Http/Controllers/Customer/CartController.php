<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Table;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function cart(string $qr_token): Response
    {
        $table = Table::active()->where('qr_token', $qr_token)->firstOrFail();

        return Inertia::render('Customer/Cart', [
            'table' => $table->only(['id', 'code', 'name']),
            'qrToken' => $qr_token,
        ]);
    }

    public function checkout(string $qr_token): Response
    {
        $table = Table::active()->where('qr_token', $qr_token)->firstOrFail();

        return Inertia::render('Customer/Checkout', [
            'table' => $table->only(['id', 'code', 'name']),
            'qrToken' => $qr_token,
            'onlinePaymentEnabled' => AppSetting::boolean('customer_online_payment_enabled', config('coffee.customer_online_payment_enabled')),
        ]);
    }
}
