<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductDeleteRequest extends FormRequest
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
            $product = $this->route('product');
            
            if (!$product) {
                $validator->errors()->add('product', 'Produk tidak ditemukan.');
                return;
            }

            if ($product->trashed()) {
                $validator->errors()->add('product', 'Produk sudah dihapus.');
                return;
            }

            if ($product->orderItems()->count() > 0) {
                $validator->errors()->add('product', 'Produk tidak dapat dihapus karena sudah digunakan dalam transaksi.');
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal menghapus produk',
            'list' => [$validator->errors()->first()]
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
