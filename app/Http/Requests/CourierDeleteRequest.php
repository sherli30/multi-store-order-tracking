<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourierDeleteRequest extends FormRequest
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
            $courier = $this->route('courier');

            if (!$courier) {
                $validator->errors()->add('courier', 'Kurir tidak ditemukan.');
                return;
            }

            // Relationship Check: Services
            if ($courier->services()->count() > 0) {
                $validator->errors()->add(
                    'services',
                    'Kurir tidak dapat dihapus karena masih memiliki layanan pengiriman aktif. Hapus layanan terkait terlebih dahulu.'
                );
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal Menghapus Kurir',
            'list' => $validator->errors()->all()
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
