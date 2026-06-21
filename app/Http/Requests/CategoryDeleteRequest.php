<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryDeleteRequest extends FormRequest
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
            $category = $this->route('product_category');
            
            if (!$category) {
                $validator->errors()->add('category', 'Kategori tidak ditemukan.');
                return;
            }

            if ($category->products()->withTrashed()->count() > 0) {
                $validator->errors()->add('category', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal menghapus kategori produk',
            'list' => [$validator->errors()->first()]
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
