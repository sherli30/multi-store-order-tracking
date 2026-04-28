<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|min:3|max:255',
            'phone'   => 'nullable|string|min:10|max:20',
            'address' => 'nullable|string|min:5',
            'avatar'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap tidak boleh kosong.',
            'name.min'      => 'Nama minimal harus terdiri dari 3 karakter.',
            'phone.min'     => 'Nomor HP tidak valid. Minimal 10 digit.',
            'phone.max'     => 'Nomor HP terlalu panjang.',
            'address.min'   => 'Alamat terlalu singkat. Mohon masukkan alamat yang lebih detail.',
            'avatar.image'  => 'File yang Anda pilih bukan gambar.',
            'avatar.mimes'  => 'Format foto profil harus JPG, JPEG, atau PNG.',
            'avatar.max'    => 'Ukuran foto terlalu besar. Maksimal adalah 2MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => $validator->errors()->first(),
            'errors'  => $validator->errors()
        ], 422));
    }
}
