<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'tracking_number' => ['required', 'string', 'max:100'],
            'shipping_courier' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'tracking_number.required' => 'Nomor resi wajib diisi.',
            'tracking_number.string'   => 'Nomor resi harus berupa teks.',
            'tracking_number.max'      => 'Nomor resi maksimal 100 karakter.',
            'shipping_courier.required' => 'Kurir pengiriman wajib diisi.',
            'shipping_courier.string'  => 'Kurir pengiriman harus berupa teks.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()
            ->back()
            ->withInput()
            ->withErrors($validator, $this->errorBag);

        if ($this->input('source') !== 'show') {
            $response->with('open_modal', 'shipModal');
        }

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
