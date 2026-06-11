<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'tracking_number' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'tracking_number.required' => 'Nomor resi pengiriman wajib diisi.',
            'tracking_number.string' => 'Nomor resi harus berupa teks/angka.',
            'tracking_number.max' => 'Nomor resi terlalu panjang, maksimal 100 karakter.',
        ];
    }
}
