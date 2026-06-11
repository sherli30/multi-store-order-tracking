<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryUpdateTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'identifier'       => ['required', 'string'],
            'status'           => ['required', 'string', 'in:processing,shipping,completed'],
            'shipping_courier' => ['nullable', 'string'],
            'tracking_number'  => ['nullable', 'string'],
            'notes'            => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Nomor Resi atau Nomor Pesanan wajib diisi (bisa dari hasil scan).',
            'identifier.string' => 'Identifikasi harus berupa teks/karakter.',
            
            'status.required' => 'Status pengiriman wajib dipilih.',
            'status.in' => 'Status pengiriman tidak valid.',
            
            'shipping_courier.string' => 'Nama kurir harus berupa teks.',
            'tracking_number.string' => 'Nomor resi harus berupa teks/angka.',
            'notes.string' => 'Catatan harus berupa teks.',
        ];
    }
}
