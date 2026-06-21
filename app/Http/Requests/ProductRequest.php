<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'store_id'    => ['required', 'exists:stores,id'],
            'category_id' => [
                'required',
                Rule::exists('product_categories', 'id')->where('store_id', $this->store_id),
            ],
            'name'        => ['required', 'string', 'max:255'],
            'is_active'   => $this->isMethod('post') ? ['accepted'] : ['required', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sku'         => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($this->route('product'))
            ],

            // Price, Stock & Weight
            'price'  => ['required', 'numeric', 'min:0', 'max:999999999'],
            'stock'  => [$this->isMethod('POST') ? 'required' : 'nullable', 'integer', 'min:0', 'max:999999'],
            'weight' => ['required', 'numeric', 'min:0', 'max:999999'],

            // Multiple Images
            'images'   => [$this->isMethod('POST') ? 'required' : 'nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'replace_images'   => ['nullable', 'array'],
            'replace_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            // ID gambar lama yang dihapus
            'deleted_images'   => ['nullable', 'array'],
            'deleted_images.*' => ['exists:product_images,id'],
            'primary_image_index' => ['nullable', 'integer'],
            'primary_image_id'    => ['nullable', 'exists:product_images,id'],

            // Descriptions
            'descriptions' => ['nullable', 'array'],
            'descriptions.*.id' => ['nullable', 'exists:product_descriptions,id'],
            'descriptions.*.title' => ['required', 'string', 'max:255'],
            'descriptions.*.content' => ['required', 'string'],

            // Specifications
            'specifications' => ['nullable', 'array'],
            'specifications.*.id' => ['nullable', 'exists:product_specifications,id'],
            'specifications.*.name' => ['required', 'string', 'max:255'],
            'specifications.*.value' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            // ── Informasi Dasar ───────────────────────────────────────────────
            'store_id.required'    => 'Toko wajib dipilih. Pilih toko yang akan menampung produk ini.',
            'store_id.exists'      => 'Toko yang dipilih tidak ditemukan. Muat ulang halaman dan coba lagi.',
            'category_id.required' => 'Kategori produk wajib dipilih. Pastikan toko sudah dipilih terlebih dahulu.',
            'category_id.exists'   => 'Kategori tidak valid atau tidak termasuk dalam toko yang dipilih.',
            'name.required'        => 'Nama produk wajib diisi.',
            'name.max'             => 'Nama produk terlalu panjang. Maksimal 255 karakter.',
            'sku.required'         => 'SKU wajib diisi.',
            'sku.unique'           => 'SKU sudah digunakan.',
            'sku.max'              => 'SKU terlalu panjang. Maksimal 255 karakter.',

            // ── Harga, Stok & Berat (Single Variant) ─────────────────────────
            'price.required'       => 'Harga wajib diisi.',
            'price.numeric'        => 'Harga harus berupa angka. Jangan sertakan tanda titik atau koma.',
            'price.min'            => 'Harga harus lebih besar dari 0.',
            'price.max'            => 'Harga yang dimasukkan terlalu besar. Maksimal Rp 999.999.999.',
            'stock.required'       => 'Stok wajib diisi.',
            'stock.integer'        => 'Stok harus berupa bilangan bulat, bukan desimal.',
            'stock.min'            => 'Stok tidak boleh negatif.',
            'stock.max'            => 'Jumlah stok terlalu besar. Maksimal 999.999 unit.',
            'weight.required'      => 'Berat produk wajib diisi.',
            'weight.numeric'       => 'Berat harus berupa angka (contoh: 0.5 kg atau 1 kg).',
            'weight.min'           => 'Berat harus lebih besar dari 0.',
            'weight.max'           => 'Berat yang dimasukkan terlalu besar. Maksimal 9999 kg.',

            // ── Foto Produk ───────────────────────────────────────────────────
            'images.required'      => 'Gambar produk wajib diunggah.',
            'images.*.image'       => 'File yang diunggah bukan gambar. Pastikan file bertipe gambar.',
            'images.*.mimes'       => 'Format gambar tidak valid. Gunakan file .jpg, .jpeg, .png, atau .webp.',
            'images.*.max'         => 'Ukuran gambar melebihi batas maksimum.',
            'replace_images.*.image' => 'File pengganti bukan gambar. Pastikan file bertipe gambar.',
            'replace_images.*.mimes' => 'Format gambar pengganti tidak didukung. Gunakan .jpg, .jpeg, .png, atau .webp.',
            'replace_images.*.max'   => 'Ukuran gambar melebihi batas maksimum.',
            'deleted_images.*.exists'  => 'Foto yang ingin dihapus tidak ditemukan. Muat ulang halaman dan coba lagi.',
            'primary_image_id.exists'  => 'Foto utama yang dipilih tidak ditemukan. Pilih ulang foto utama produk.',

            // ── Deskripsi ─────────────────────────────────────────────────────
            'descriptions.*.title.required'   => 'Judul deskripsi tidak boleh kosong.',
            'descriptions.*.title.max'        => 'Judul deskripsi terlalu panjang. Maksimal 255 karakter.',
            'descriptions.*.content.required' => 'Konten deskripsi tidak boleh kosong. Isi dengan informasi detail produk.',

            // ── Spesifikasi ───────────────────────────────────────────────────
            'specifications.*.name.required'  => 'Nama spesifikasi tidak boleh kosong (contoh: Material, Dimensi).',
            'specifications.*.name.max'       => 'Nama spesifikasi terlalu panjang. Maksimal 255 karakter.',
            'specifications.*.value.required' => 'Nilai spesifikasi tidak boleh kosong (contoh: Kayu, 30x20x10 cm).',
            'specifications.*.value.max'      => 'Nilai spesifikasi terlalu panjang. Maksimal 255 karakter.',

            // Status
            'is_active.accepted' => 'Status produk wajib dipilih.',
            'is_active.required' => 'Status produk wajib dipilih.',
            'is_active.boolean' => 'Status produk tidak valid.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize single price if it exists
        $price = $this->price;
        if (is_string($price)) {
            $price = preg_replace('/[^0-9]/', '', $price);
        }

        $this->merge([
            'price'           => $price,
            'is_active'       => $this->boolean('is_active'),
            'is_featured'     => $this->boolean('is_featured'),
        ]);
    }
}
