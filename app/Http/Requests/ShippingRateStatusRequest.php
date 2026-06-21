<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingRateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'is_active' => 'required|boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'is_active.required' => 'Status tarif pengiriman wajib dipilih.',
            'is_active.boolean' => 'Status tarif pengiriman tidak valid.',
        ];
    }
}
