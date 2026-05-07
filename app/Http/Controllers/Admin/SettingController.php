<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Settings', [
            'customerOnlinePaymentEnabled' => AppSetting::boolean('customer_online_payment_enabled', config('coffee.customer_online_payment_enabled')),
        ]);
    }

    public function updateCustomerOnlinePayment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        AppSetting::setValue('customer_online_payment_enabled', $data['enabled']);

        return back()->with('success', 'Pengaturan pembayaran QRIS customer berhasil diperbarui.');
    }
}
