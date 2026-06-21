<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole(['administrator', 'logistics']);
    }

    protected function prepareForValidation()
    {
        // Provide default values if not present so validation can proceed
        $this->merge([
            'start_date' => $this->input('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString()),
            'end_date' => $this->input('end_date', \Carbon\Carbon::now()->toDateString()),
        ]);
    }

    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'customer_id' => ['nullable', 'exists:users,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'order_status' => ['nullable', 'string', 'in:pending,perlu_diproses,processing,shipping,completed,cancelled,refunded'],
            'payment_status' => ['nullable', 'string', 'in:unpaid,pending,settlement,capture,paid,cancel,expire,deny,refund'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal akhir wajib diisi.',
            'end_date.date' => 'Format tanggal akhir tidak valid.',
            'end_date.after_or_equal' => 'Tanggal akhir harus lebih besar atau sama dengan tanggal mulai.',
            'store_id.exists' => 'Toko tidak ditemukan.',
            'customer_id.exists' => 'Customer tidak ditemukan.',
            'product_id.exists' => 'Produk tidak ditemukan.',
            'order_status.in' => 'Status pesanan tidak valid.',
            'payment_status.in' => 'Status pembayaran tidak valid.',
            'invoice_number.max' => 'Nomor invoice tidak valid.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal memfilter laporan',
            'list' => [$validator->errors()->first()]
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
