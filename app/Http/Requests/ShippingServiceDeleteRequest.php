<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingServiceDeleteRequest extends FormRequest
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
            $shippingService = $this->route('shipping_service');

            if (!$shippingService) {
                $validator->errors()->add('shipping_service', 'Layanan pengiriman tidak ditemukan.');
                return;
            }

            // Relationship Check: Rates
            if ($shippingService->rates()->count() > 0) {
                $validator->errors()->add(
                    'rates',
                    'Layanan pengiriman tidak dapat dihapus karena masih memiliki riwayat tarif ongkir atau terkait dengan pesanan.'
                );
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal Menghapus Layanan',
            'list' => $validator->errors()->all()
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
