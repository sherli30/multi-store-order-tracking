<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id'               => 'required|exists:stores,id',
            'customer_name'          => 'required|string|max:255',
            'customer_phone'         => 'nullable|string|max:20',
            'shipping_address'       => 'required|string',
            'province'               => 'nullable|string|max:100',
            'city'                   => 'nullable|string|max:100',
            'postal_code'            => 'nullable|string|max:10',
            'shipping_type'          => 'nullable|in:reguler,cargo',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            // Store
            'store_id.required' => 'Toko wajib dipilih sebelum melanjutkan pesanan.',
            'store_id.exists'   => 'Toko yang dipilih tidak ditemukan dalam sistem.',

            // Data customer
            'customer_name.required' => 'Nama penerima wajib diisi agar kurir mengetahui tujuan pengiriman.',
            'customer_name.max'      => 'Nama penerima terlalu panjang. Maksimal 255 karakter.',
            'customer_phone.max'     => 'Nomor HP penerima terlalu panjang. Maksimal 20 digit.',

            // Pengiriman
            'shipping_address.required' => 'Alamat pengiriman wajib diisi agar pesanan dapat dikirim.',
            'shipping_type.required'    => 'Jenis pengiriman wajib dipilih (reguler atau cargo).',
            'shipping_type.in'          => 'Jenis pengiriman tidak valid. Pilih antara "reguler" atau "cargo".',

            // Wilayah
            'province.max'   => 'Nama provinsi terlalu panjang.',
            'city.max'       => 'Nama kota penerima terlalu panjang.',
            'postal_code.max' => 'Kode pos penerima terlalu panjang.',

            // Item pesanan
            'items.required'              => 'Pesanan tidak boleh kosong. Tambahkan minimal 1 produk.',
            'items.array'                 => 'Format data pesanan tidak valid.',
            'items.min'                   => 'Pesanan tidak boleh kosong. Tambahkan minimal 1 produk.',
            'items.*.product_id.required' => 'Setiap item pesanan harus memiliki produk yang dipilih.',
            'items.*.product_id.exists'   => 'Salah satu produk dalam pesanan tidak ditemukan.',
            'items.*.quantity.required'   => 'Jumlah setiap produk wajib diisi.',
            'items.*.quantity.integer'    => 'Jumlah produk harus berupa angka bulat.',
            'items.*.quantity.min'        => 'Jumlah produk minimal adalah 1 unit.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => $validator->errors()->first(),
            'errors'  => $validator->errors(),
        ], 422));
    }
}