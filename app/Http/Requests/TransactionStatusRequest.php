<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:paid,failed,refund'],
            'notes'  => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status transaksi wajib dipilih.',
            'status.string'   => 'Status transaksi harus berupa teks.',
            'status.in'       => 'Pilihan status tidak valid. Hanya bisa memilih Paid, Failed, atau Dana Dikembalikan.',
            
            'notes.string'    => 'Catatan transaksi harus berupa teks.',
            'notes.max'       => 'Catatan terlalu panjang. Maksimal 1000 karakter.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()
            ->back()
            ->withInput()
            ->withErrors($validator, $this->errorBag)
            ->with('open_modal', 'actionModal');

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
