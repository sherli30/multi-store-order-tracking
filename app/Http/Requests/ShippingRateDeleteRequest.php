<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingRateDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $shippingRate = $this->route('shipping_rate');
            
            if (!$shippingRate) {
                $validator->errors()->add('general', 'Tarif pengiriman tidak ditemukan atau sudah dihapus.');
                return;
            }

            // In our system, orders do not strictly link to shipping_rates via foreign key.
            // If they did, we would check `$shippingRate->orders()->exists()` here.
        });
    }
}
