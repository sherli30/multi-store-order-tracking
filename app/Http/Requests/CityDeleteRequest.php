<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityDeleteRequest extends FormRequest
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
            $city = $this->route('city');

            if (!$city) {
                $validator->errors()->add('city', 'Kota tidak ditemukan.');
                return;
            }

            if ($city->stores()->count() > 0) {
                $validator->errors()->add(
                    'stores',
                    'Kota tidak dapat dihapus karena masih digunakan oleh toko.'
                );
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal Menghapus Kota',
            'list' => $validator->errors()->all()
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
