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
            'is_active'   => ['nullable', 'boolean'],

            // Variasi
            'has_variants' => ['nullable', 'boolean'],

            // Single variant fallback
            'price'  => ['exclude_if:has_variants,true', 'required', 'numeric', 'min:0', 'max:999999999'],
            'stock'  => ['exclude_if:has_variants,true', $this->isMethod('POST') ? 'required' : 'nullable', 'integer', 'min:0', 'max:999999'],
            'weight' => ['exclude_if:has_variants,true', 'required', 'numeric', 'min:0', 'max:999999'],

            // Multiple variants array
            'variants'          => ['exclude_unless:has_variants,true', 'required', 'array', 'min:1'],
            'variants.*.id'     => ['nullable', 'exists:product_variants,id'],
            'variants.*.name'   => ['required', 'string', 'max:255'],
            'variants.*.sku'    => ['nullable', 'string', 'max:100'],
            'variants.*.price'  => ['required', 'numeric', 'min:0'],
            'variants.*.stock'  => [$this->isMethod('POST') ? 'required' : 'nullable', 'integer', 'min:0'],
            'variants.*.weight' => ['required', 'numeric', 'min:0'],

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

            // Packing Options
            'packing_options' => ['nullable', 'array'],
            'packing_options.*.id' => ['nullable', 'exists:packing_options,id'],
            'packing_options.*.name' => ['required', 'string', 'max:255'],
            'packing_options.*.extra_price' => ['required', 'numeric', 'min:0'],
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
            'name.required'        => 'Nama produk tidak boleh kosong. Masukkan nama yang jelas dan mudah dikenali.',
            'name.max'             => 'Nama produk terlalu panjang. Maksimal 255 karakter.',

            // ── Harga, Stok & Berat (Single Variant) ─────────────────────────
            'price.required'       => 'Harga produk wajib diisi.',
            'price.numeric'        => 'Harga harus berupa angka. Jangan sertakan tanda titik atau koma.',
            'price.min'            => 'Harga tidak boleh bernilai negatif.',
            'price.max'            => 'Harga yang dimasukkan terlalu besar. Maksimal Rp 999.999.999.',
            'stock.required'       => 'Jumlah stok awal wajib diisi. Masukkan 0 jika produk belum tersedia.',
            'stock.integer'        => 'Stok harus berupa bilangan bulat, bukan desimal.',
            'stock.min'            => 'Stok tidak boleh bernilai negatif.',
            'stock.max'            => 'Jumlah stok terlalu besar. Maksimal 999.999 unit.',
            'weight.required'      => 'Berat produk wajib diisi. Berat digunakan untuk kalkulasi ongkos kirim.',
            'weight.numeric'       => 'Berat harus berupa angka (contoh: 500 untuk 500 gram).',
            'weight.min'           => 'Berat tidak boleh bernilai negatif.',

            // ── Variasi Produk ────────────────────────────────────────────────
            'variants.required'          => 'Tambahkan setidaknya 1 variasi produk sebelum menyimpan.',
            'variants.min'               => 'Produk dengan variasi harus memiliki minimal 1 variasi.',
            'variants.*.name.required'   => 'Nama variasi tidak boleh kosong (contoh: Merah, XL, 1 Kg).',
            'variants.*.name.max'        => 'Nama variasi terlalu panjang. Maksimal 255 karakter.',
            'variants.*.sku.max'         => 'SKU variasi terlalu panjang. Maksimal 100 karakter.',
            'variants.*.price.required'  => 'Harga wajib diisi untuk setiap variasi.',
            'variants.*.price.numeric'   => 'Harga variasi harus berupa angka.',
            'variants.*.price.min'       => 'Harga variasi tidak boleh bernilai negatif.',
            'variants.*.stock.required'  => 'Stok awal wajib diisi untuk setiap variasi. Masukkan 0 jika belum tersedia.',
            'variants.*.stock.integer'   => 'Stok variasi harus berupa bilangan bulat.',
            'variants.*.stock.min'       => 'Stok variasi tidak boleh bernilai negatif.',
            'variants.*.weight.required' => 'Berat wajib diisi untuk setiap variasi.',
            'variants.*.weight.numeric'  => 'Berat variasi harus berupa angka.',
            'variants.*.weight.min'      => 'Berat variasi tidak boleh bernilai negatif.',

            // ── Foto Produk ───────────────────────────────────────────────────
            'images.required'      => 'Foto produk wajib diunggah. Tambahkan minimal 1 foto agar produk terlihat menarik.',
            'images.*.image'       => 'File yang diunggah bukan gambar. Pastikan file bertipe gambar.',
            'images.*.mimes'       => 'Format gambar tidak didukung. Gunakan file .jpg, .jpeg, .png, atau .webp.',
            'images.*.max'         => 'Ukuran foto terlalu besar. Maksimal 2 MB per file.',
            'replace_images.*.image' => 'File pengganti bukan gambar. Pastikan file bertipe gambar.',
            'replace_images.*.mimes' => 'Format gambar pengganti tidak didukung. Gunakan .jpg, .jpeg, .png, atau .webp.',
            'replace_images.*.max'   => 'Ukuran foto pengganti terlalu besar. Maksimal 2 MB per file.',

            // ── Deskripsi ─────────────────────────────────────────────────────
            'descriptions.*.title.required'   => 'Judul deskripsi tidak boleh kosong.',
            'descriptions.*.title.max'        => 'Judul deskripsi terlalu panjang. Maksimal 255 karakter.',
            'descriptions.*.content.required' => 'Konten deskripsi tidak boleh kosong. Isi dengan informasi detail produk.',

            // ── Spesifikasi ───────────────────────────────────────────────────
            'specifications.*.name.required'  => 'Nama spesifikasi tidak boleh kosong (contoh: Material, Dimensi).',
            'specifications.*.name.max'       => 'Nama spesifikasi terlalu panjang. Maksimal 255 karakter.',
            'specifications.*.value.required' => 'Nilai spesifikasi tidak boleh kosong (contoh: Kayu, 30x20x10 cm).',
            'specifications.*.value.max'      => 'Nilai spesifikasi terlalu panjang. Maksimal 255 karakter.',

            // ── Opsi Packing ──────────────────────────────────────────────────
            'packing_options.*.name.required'        => 'Nama opsi packing tidak boleh kosong (contoh: Bubble Wrap, Kayu).',
            'packing_options.*.name.max'             => 'Nama opsi packing terlalu panjang. Maksimal 255 karakter.',
            'packing_options.*.extra_price.required' => 'Harga tambahan opsi packing wajib diisi. Masukkan 0 jika gratis.',
            'packing_options.*.extra_price.numeric'  => 'Harga tambahan packing harus berupa angka.',
            'packing_options.*.extra_price.min'      => 'Harga tambahan packing tidak boleh bernilai negatif.',
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

        // Sanitize variant prices if they exist
        $variants = $this->variants;
        if (is_array($variants)) {
            foreach ($variants as $key => $variant) {
                if (isset($variant['price']) && is_string($variant['price'])) {
                    $variants[$key]['price'] = preg_replace('/[^0-9]/', '', $variant['price']);
                }
                if (isset($variant['weight']) && is_string($variant['weight'])) {
                    $variants[$key]['weight'] = preg_replace('/[^0-9.]/', '', $variant['weight']);
                }
            }
        }

        $packingOptions = $this->packing_options;
        if (is_array($packingOptions)) {
            foreach ($packingOptions as $key => $po) {
                if (isset($po['extra_price']) && is_string($po['extra_price'])) {
                    $packingOptions[$key]['extra_price'] = preg_replace('/[^0-9]/', '', $po['extra_price']);
                }
            }
        }

        $this->merge([
            'price'           => $price,
            'is_active'       => $this->has('is_active'),
            'has_variants'    => $this->has('has_variants'),
            'variants'        => $variants,
            'packing_options' => $packingOptions,
        ]);
    }
}
