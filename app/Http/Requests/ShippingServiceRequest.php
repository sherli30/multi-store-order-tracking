<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingServiceRequest extends FormRequest
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
        $rules = [
            'courier_id' => [
                'required',
                'exists:couriers,id'
            ],
            'service_name' => [
                'required',
                'string',
                'max:255'
            ],
            'min_weight' => [
                'required',
                'integer',
                'min:0'
            ],
        ];

        if ($this->isMethod('POST')) {
            $rules['is_active'] = ['accepted'];
        } else {
            $rules['is_active'] = ['required', 'boolean'];
        }

        return $rules;
    }

    /**
     * Get custom validation messages.
     * Dibuat sangat detail untuk setiap kondisi input dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            // Kurir (courier_id)
            'courier_id.required' => 'Ekspedisi logistik wajib dipilih. Silakan pilih salah satu ekspedisi dari daftar yang tersedia.',
            'courier_id.exists' => 'Ekspedisi logistik yang Anda pilih tidak terdaftar di sistem kami.',

            // Nama Layanan (service_name)
            'service_name.required' => 'Nama layanan wajib diisi. Silakan masukkan nama jenis pengiriman (contoh: Reguler, Cargo, OKE).',
            'service_name.string' => 'Nama layanan harus berupa karakter teks biasa yang valid.',
            'service_name.max' => 'Nama layanan terlalu panjang. Maksimal adalah 255 karakter.',

            // Minimal Berat (min_weight)
            'min_weight.required' => 'Batas minimal berat wajib diisi. Masukkan angka 0 jika tidak ada batas minimal.',
            'min_weight.integer' => 'Batas minimal berat harus berupa bilangan bulat (angka).',
            'min_weight.min' => 'Batas minimal berat tidak boleh bernilai negatif. Minimal adalah 0 gram.',

            // Status Aktif (is_active)
            'is_active.accepted' => 'Status aktif layanan pengiriman wajib diaktifkan saat penambahan baru.',
            'is_active.required' => 'Status aktif layanan pengiriman wajib diisi. Silakan tentukan apakah layanan ini aktif atau tidak.',
            'is_active.boolean' => 'Status aktif layanan pengiriman harus berupa pilihan benar (aktif) atau salah (non-aktif).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()
            ->back()
            ->withInput()
            ->withErrors($validator, $this->errorBag)
            ->with('open_modal', 'serviceModal');

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
