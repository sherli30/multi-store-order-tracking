<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('administrator');
    }

    public function rules(): array
    {
        return [];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $store = $this->route('store');
            
            if (!$store) {
                $validator->errors()->add('store', 'Toko tidak ditemukan.');
                return;
            }

            if ($store->productCategories()->count() > 0) {
                $validator->errors()->add('store', 'Toko tidak dapat dihapus karena masih memiliki produk (kategori).');
            }

            if ($store->products()->withTrashed()->count() > 0) {
                $validator->errors()->add('store', 'Toko tidak dapat dihapus karena masih memiliki produk.');
            }

            if ($store->orders()->count() > 0) {
                $validator->errors()->add('store', 'Toko tidak dapat dihapus karena masih memiliki transaksi.');
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal menghapus toko',
            'list' => [$validator->errors()->first()]
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
