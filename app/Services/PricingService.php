<?php

namespace App\Services;

use App\Models\AppSetting;

class PricingService
{
    public function settings(): array
    {
        return [
            'tax_enabled' => AppSetting::boolean('tax_enabled', true),
            'tax_rate' => (float) AppSetting::getValue('tax_rate', '11'),
            'service_charge_enabled' => AppSetting::boolean('service_charge_enabled', false),
            'service_charge_rate' => (float) AppSetting::getValue('service_charge_rate', '0'),
        ];
    }

    public function calculate(float $subtotal, float $discount = 0): array
    {
        $settings = $this->settings();
        $base = max($subtotal - $discount, 0);

        $tax = $settings['tax_enabled']
            ? round($base * $settings['tax_rate'] / 100, 2)
            : 0;

        $serviceCharge = $settings['service_charge_enabled']
            ? round($base * $settings['service_charge_rate'] / 100, 2)
            : 0;

        return [
            'base' => round($base, 2),
            'tax' => $tax,
            'service_charge' => $serviceCharge,
            'total' => round($base + $tax + $serviceCharge, 2),
        ];
    }
}
