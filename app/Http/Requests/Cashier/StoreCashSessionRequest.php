<?php

namespace App\Http\Requests\Cashier;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'opening_cash' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'opening_cash.required' => 'Jumlah kas awal wajib diisi.',
            'opening_cash.min' => 'Jumlah kas awal tidak boleh negatif.',
        ];
    }
}
