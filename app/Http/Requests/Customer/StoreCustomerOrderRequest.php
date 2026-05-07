<?php

namespace App\Http\Requests\Customer;

use App\Models\AppSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $paymentMethods = AppSetting::boolean('customer_online_payment_enabled', config('coffee.customer_online_payment_enabled'))
            ? ['cashier', 'qris']
            : ['cashier'];

        return [
            'customer_name' => ['nullable', 'string', 'max:255'],
            'payment_method' => ['required', Rule::in($paymentMethods)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.variant_id' => ['nullable', 'exists:product_variants,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.product_name' => ['required', 'string'],
            'items.*.variant_name' => ['nullable', 'string'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
