<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCancelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'cancel_reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'cancel_reason.required' => 'Alasan pembatalan wajib diisi agar tercatat di riwayat.',
            'cancel_reason.string' => 'Alasan pembatalan harus berupa teks.',
            'cancel_reason.max' => 'Alasan pembatalan terlalu panjang, maksimal 1000 karakter.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()
            ->back()
            ->withInput()
            ->withErrors($validator, $this->errorBag)
            ->with('open_modal', 'cancelModal');

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
