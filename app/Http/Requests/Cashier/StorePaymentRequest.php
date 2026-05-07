<?php

namespace App\Http\Requests\Cashier;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'method' => ['required', 'in:cash,qris,transfer,ewallet'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'method.in' => 'Metode pembayaran tidak valid.',
            'amount.min' => 'Jumlah pembayaran tidak boleh negatif.',
        ];
    }
}
