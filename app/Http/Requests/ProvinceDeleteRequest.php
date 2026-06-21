<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProvinceDeleteRequest extends FormRequest
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
            $province = $this->route('province');

            if (!$province) {
                $validator->errors()->add('province', 'Provinsi tidak ditemukan.');
                return;
            }

            if ($province->cities()->count() > 0) {
                $validator->errors()->add(
                    'cities',
                    'Provinsi tidak dapat dihapus karena masih memiliki kota. Hapus atau pindahkan kota terlebih dahulu.'
                );
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal Menghapus Provinsi',
            'list' => $validator->errors()->all()
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
