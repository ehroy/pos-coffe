<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Owner/Settings', [
            'settings' => [
                'customer_online_payment_enabled' => AppSetting::boolean('customer_online_payment_enabled', config('coffee.customer_online_payment_enabled')),
                'payment_gateway_provider' => AppSetting::getValue('payment_gateway_provider', 'midtrans'),
                'payment_gateway_client_key' => AppSetting::getValue('payment_gateway_client_key', ''),
                'payment_gateway_secret_key' => AppSetting::getValue('payment_gateway_secret_key', ''),
                'payment_gateway_merchant_id' => AppSetting::getValue('payment_gateway_merchant_id', ''),
                'printer_name' => AppSetting::getValue('printer_name', ''),
                'printer_ip' => AppSetting::getValue('printer_ip', ''),
                'printer_port' => AppSetting::getValue('printer_port', '9100'),
                'tax_enabled' => AppSetting::boolean('tax_enabled', true),
                'tax_rate' => AppSetting::getValue('tax_rate', '11'),
                'service_charge_enabled' => AppSetting::boolean('service_charge_enabled', false),
                'service_charge_rate' => AppSetting::getValue('service_charge_rate', '0'),
                'integration_enabled' => AppSetting::boolean('integration_enabled', false),
                'integration_webhook_url' => AppSetting::getValue('integration_webhook_url', ''),
                'integration_api_key' => AppSetting::getValue('integration_api_key', ''),
                'integration_secret_key' => AppSetting::getValue('integration_secret_key', ''),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_online_payment_enabled' => ['required', 'boolean'],
            'payment_gateway_provider' => ['nullable', 'string', 'max:50'],
            'payment_gateway_client_key' => ['nullable', 'string', 'max:255'],
            'payment_gateway_secret_key' => ['nullable', 'string', 'max:255'],
            'payment_gateway_merchant_id' => ['nullable', 'string', 'max:255'],
            'printer_name' => ['nullable', 'string', 'max:100'],
            'printer_ip' => ['nullable', 'string', 'max:45'],
            'printer_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'tax_enabled' => ['required', 'boolean'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'service_charge_enabled' => ['required', 'boolean'],
            'service_charge_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'integration_enabled' => ['required', 'boolean'],
            'integration_webhook_url' => ['nullable', 'url', 'max:255'],
            'integration_api_key' => ['nullable', 'string', 'max:255'],
            'integration_secret_key' => ['nullable', 'string', 'max:255'],
        ]);

        AppSetting::setValue('customer_online_payment_enabled', $data['customer_online_payment_enabled']);
        AppSetting::setValue('payment_gateway_provider', $data['payment_gateway_provider'] ?? 'midtrans');
        AppSetting::setValue('payment_gateway_client_key', $data['payment_gateway_client_key'] ?? '');
        AppSetting::setValue('payment_gateway_secret_key', $data['payment_gateway_secret_key'] ?? '');
        AppSetting::setValue('payment_gateway_merchant_id', $data['payment_gateway_merchant_id'] ?? '');
        AppSetting::setValue('printer_name', $data['printer_name'] ?? '');
        AppSetting::setValue('printer_ip', $data['printer_ip'] ?? '');
        AppSetting::setValue('printer_port', $data['printer_port'] ?? '9100');
        AppSetting::setValue('tax_enabled', $data['tax_enabled']);
        AppSetting::setValue('tax_rate', $data['tax_rate'] ?? '11');
        AppSetting::setValue('service_charge_enabled', $data['service_charge_enabled']);
        AppSetting::setValue('service_charge_rate', $data['service_charge_rate'] ?? '0');
        AppSetting::setValue('integration_enabled', $data['integration_enabled']);
        AppSetting::setValue('integration_webhook_url', $data['integration_webhook_url'] ?? '');
        AppSetting::setValue('integration_api_key', $data['integration_api_key'] ?? '');
        AppSetting::setValue('integration_secret_key', $data['integration_secret_key'] ?? '');

        return back()->with('success', 'Pengaturan owner berhasil diperbarui.');
    }

    public function updateCustomerOnlinePayment(Request $request): RedirectResponse
    {
        return $this->update(new Request([
            'customer_online_payment_enabled' => $request->boolean('enabled'),
            'payment_gateway_provider' => AppSetting::getValue('payment_gateway_provider', 'midtrans'),
            'payment_gateway_client_key' => AppSetting::getValue('payment_gateway_client_key', ''),
            'payment_gateway_secret_key' => AppSetting::getValue('payment_gateway_secret_key', ''),
            'payment_gateway_merchant_id' => AppSetting::getValue('payment_gateway_merchant_id', ''),
            'printer_name' => AppSetting::getValue('printer_name', ''),
            'printer_ip' => AppSetting::getValue('printer_ip', ''),
            'printer_port' => AppSetting::getValue('printer_port', '9100'),
            'tax_enabled' => AppSetting::boolean('tax_enabled', true),
            'tax_rate' => AppSetting::getValue('tax_rate', '11'),
            'service_charge_enabled' => AppSetting::boolean('service_charge_enabled', false),
            'service_charge_rate' => AppSetting::getValue('service_charge_rate', '0'),
            'integration_enabled' => AppSetting::boolean('integration_enabled', false),
            'integration_webhook_url' => AppSetting::getValue('integration_webhook_url', ''),
            'integration_api_key' => AppSetting::getValue('integration_api_key', ''),
            'integration_secret_key' => AppSetting::getValue('integration_secret_key', ''),
        ]));
    }
}
