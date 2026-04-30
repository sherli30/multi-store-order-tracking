<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockRequest extends FormRequest
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
            'qty' => [
                'required',
                'integer',
                'min:1',
                'max:999999'
            ],

            'note' => [
                'required',
                'string',
                'max:500'
            ],

            // Tambahan: Validasi tipe transaksi jika Anda menggunakannya (misal: penambahan/pengurangan)
            'type' => [
                'nullable',
                'string',
                'in:in,out'
            ],
        ];
    }

    /**
     * Get custom validation messages.
     * Dibuat sangat detail untuk setiap kondisi input.
     */
    public function messages(): array
    {
        return [
            // Quantity (qty)
            'qty.required' => 'Jumlah stok wajib diisi. Masukkan angka unit yang ingin ditambah atau dikurangi.',
            'qty.integer'  => 'Jumlah stok harus berupa angka bulat, bukan desimal (contoh: 10, bukan 10.5).',
            'qty.min'      => 'Jumlah stok minimal adalah 1 unit. Nilai 0 tidak diperbolehkan untuk penyesuaian stok.',
            'qty.max'      => 'Jumlah stok terlalu besar. Maksimal penyesuaian sekaligus adalah 999.999 unit.',

            // Note (Catatan)
            'note.required' => 'Catatan penyesuaian stok wajib diisi. Tuliskan alasan perubahan stok ini (contoh: Restok dari supplier, Barang rusak).',
            'note.string'   => 'Catatan harus berupa teks biasa.',
            'note.max'      => 'Catatan terlalu panjang. Maksimal 500 karakter, cukup tuliskan poin penting saja.',

            // Type (Opsional jika digunakan)
            'type.string'   => 'Tipe transaksi harus berupa teks.',
            'type.in'       => 'Tipe transaksi tidak valid. Pilihan yang tersedia hanya "in" (stok masuk) atau "out" (stok keluar).',
        ];
    }

    /**
     * Prepare the data for validation.
     * Membersihkan input dari karakter non-angka jika user input manual dengan titik/koma.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('qty')) {
            $qty = $this->qty;

            // Jika qty dikirim dalam format ribuan (misal: 1.000), bersihkan menjadi angka murni (1000)
            if (is_string($qty)) {
                $qty = preg_replace('/[^0-9]/', '', $qty);
            }

            $this->merge([
                'qty' => $qty,
            ]);
        }
    }
}
