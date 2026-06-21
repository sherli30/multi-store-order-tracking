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

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
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
            'origin_province_id' => [
                'required',
                'exists:provinces,id'
            ],
            'origin_city_id' => [
                'required',
                'exists:cities,id'
            ],
            'destination_province_id' => [
                'required',
                'exists:provinces,id'
            ],
            'destination_city_id' => [
                'required',
                'exists:cities,id'
            ],
            'min_weight' => [
                'required',
                'numeric',
                'min:0.01'
            ],
            'max_weight' => [
                'nullable',
                'numeric',
                'gt:min_weight'
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
            'is_active' => [
                'sometimes',
                'boolean'
            ],
        ];

    /**
     * Get custom validation messages in Indonesian.
     */
        return [
            'shipping_service_id.required' => 'Layanan pengiriman wajib dipilih.',
            'shipping_service_id.exists' => 'Layanan pengiriman tidak ditemukan.',
            
            'origin_province_id.required' => 'Provinsi asal wajib dipilih.',
            'origin_province_id.exists' => 'Provinsi asal tidak ditemukan.',
            
            'origin_city_id.required' => 'Kota asal wajib dipilih.',
            'origin_city_id.exists' => 'Kota asal tidak ditemukan.',
            
            'destination_province_id.required' => 'Provinsi tujuan wajib dipilih.',
            'destination_province_id.exists' => 'Provinsi tujuan tidak ditemukan.',
            
            'destination_city_id.required' => 'Kota tujuan wajib dipilih.',
            'destination_city_id.exists' => 'Kota tujuan tidak ditemukan.',
            
            'min_weight.required' => 'Berat minimum wajib diisi.',
            'min_weight.numeric' => 'Berat minimum harus berupa angka.',
            'min_weight.min' => 'Berat minimum harus lebih besar dari 0.',
            
            'max_weight.numeric' => 'Berat maksimum harus berupa angka.',
            'max_weight.gt' => 'Berat maksimum harus lebih besar dari berat minimum.',
            
            'cost_per_kg.required' => 'Ongkos kirim wajib diisi.',
            'cost_per_kg.numeric' => 'Ongkos kirim harus berupa angka.',
            'cost_per_kg.min' => 'Ongkos kirim harus lebih besar dari atau sama dengan 0.',
            
            'etd_min.required' => 'Estimasi pengiriman wajib diisi.',
            'etd_min.integer' => 'Estimasi pengiriman tidak valid.',
            'etd_min.min' => 'Estimasi pengiriman tidak valid.',
            
            'etd_max.required' => 'Estimasi pengiriman wajib diisi.',
            'etd_max.integer' => 'Estimasi pengiriman tidak valid.',
            'etd_max.min' => 'Estimasi pengiriman tidak valid.',
            'etd_max.gte' => 'Estimasi maksimal harus lebih besar atau sama dengan estimasi minimal.',

            'is_active.boolean' => 'Status tarif pengiriman tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'shipping_service_id' => 'layanan pengiriman',
            'origin_province_id' => 'provinsi asal',
            'origin_city_id' => 'kota asal',
            'destination_province_id' => 'provinsi tujuan',
            'destination_city_id' => 'kota tujuan',
            'min_weight' => 'berat minimum',
            'max_weight' => 'berat maksimum',
            'cost_per_kg' => 'ongkos kirim',
            'etd_min' => 'estimasi pengiriman minimal',
            'etd_max' => 'estimasi pengiriman maksimal',
            'is_active' => 'status',
        ];

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
