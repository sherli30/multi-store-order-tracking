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
            'province' => 'required|string|max:100',
            'city'    => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'avatar'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            // Nama
            'name.required' => 'Nama lengkap tidak boleh kosong.',
            'name.string'   => 'Nama harus berupa teks, bukan angka atau karakter khusus.',
            'name.min'      => 'Nama minimal harus terdiri dari 3 karakter.',
            'name.max'      => 'Nama terlalu panjang. Maksimal 255 karakter.',

            // Nomor HP
            'phone.string'  => 'Nomor HP harus berupa teks angka, bukan format lain.',
            'phone.min'     => 'Nomor HP tidak valid. Minimal 10 digit.',
            'phone.max'     => 'Nomor HP terlalu panjang. Maksimal 20 digit.',

            // Alamat
            'address.string' => 'Alamat harus berupa teks.',
            'address.min'    => 'Alamat terlalu singkat. Mohon masukkan alamat yang lebih detail.',

            // Wilayah
            'province.required' => 'Provinsi wajib dipilih.',
            'province.string' => 'Nama provinsi harus berupa teks.',
            'city.required'   => 'Nama kota tidak boleh kosong.',
            'city.string'    => 'Nama kota harus berupa teks.',
            'city.max'       => 'Nama kota terlalu panjang.',
            'postal_code.required' => 'Kode pos tidak boleh kosong.',
            'postal_code.max' => 'Kode pos maksimal 10 karakter.',

            // Avatar / Foto
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
