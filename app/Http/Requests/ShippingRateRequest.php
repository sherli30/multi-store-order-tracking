<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingRateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'shipping_service_id' => [
                'required',
                'exists:shipping_services,id'
            ],
            'origin_city_id' => [
                'required',
                'exists:cities,id'
            ],
            'destination_city_id' => [
                'required',
                'exists:cities,id'
            ],
            'cost_per_kg' => [
                'required',
                'numeric',
                'min:0'
            ],
            'etd_min' => [
                'required',
                'integer',
                'min:1'
            ],
            'etd_max' => [
                'required',
                'integer',
                'min:1',
                'gte:etd_min'
            ],
        ];
    }

    /**
     * Get custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'shipping_service_id.required' => 'Layanan kurir wajib dipilih. Silakan tentukan kurir dan nama layanan.',
            'shipping_service_id.exists' => 'Layanan kurir yang dipilih tidak terdaftar di sistem.',
            'origin_city_id.required' => 'Kota asal pengiriman wajib dipilih. Silakan tentukan kota asal.',
            'origin_city_id.exists' => 'Kota asal pengiriman yang dipilih tidak valid.',
            'destination_city_id.required' => 'Kota tujuan pengiriman wajib dipilih. Silakan tentukan kota tujuan.',
            'destination_city_id.exists' => 'Kota tujuan pengiriman yang dipilih tidak valid.',
            'cost_per_kg.required' => 'Biaya pengiriman per kilogram wajib diisi.',
            'cost_per_kg.numeric' => 'Biaya pengiriman per kilogram harus berupa angka numerik.',
            'cost_per_kg.min' => 'Biaya pengiriman per kilogram tidak boleh kurang dari Rp 0.',
            'etd_min.required' => 'Estimasi minimal waktu pengiriman wajib diisi.',
            'etd_min.integer' => 'Estimasi minimal harus berupa bilangan bulat.',
            'etd_min.min' => 'Estimasi minimal waktu pengiriman tidak boleh kurang dari 1 hari.',
            'etd_max.required' => 'Estimasi maksimal waktu pengiriman wajib diisi.',
            'etd_max.integer' => 'Estimasi maksimal harus berupa bilangan bulat.',
            'etd_max.min' => 'Estimasi maksimal waktu pengiriman tidak boleh kurang dari 1 hari.',
            'etd_max.gte' => 'Estimasi maksimal waktu pengiriman harus lebih besar atau sama dengan estimasi minimal.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()
            ->back()
            ->withInput()
            ->withErrors($validator, $this->errorBag)
            ->with('open_modal', 'rateModal');

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
