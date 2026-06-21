<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;

class ReportExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole(['administrator', 'logistics']);
    }

    protected function prepareForValidation()
    {
        if (!$this->has('start_date') || !$this->has('end_date')) {
            $this->merge([
                'start_date' => $this->input('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString()),
                'end_date' => $this->input('end_date', \Carbon\Carbon::now()->toDateString()),
            ]);
        }
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
            'start_date.required' => 'Parameter ekspor tidak lengkap. Tanggal mulai wajib diisi.',
            'start_date.date' => 'Periode laporan tidak valid. Format tanggal mulai tidak valid.',
            'end_date.required' => 'Parameter ekspor tidak lengkap. Tanggal akhir wajib diisi.',
            'end_date.date' => 'Periode laporan tidak valid. Format tanggal akhir tidak valid.',
            'end_date.after_or_equal' => 'Periode laporan tidak valid. Tanggal akhir harus lebih besar atau sama dengan tanggal mulai.',
            'store_id.exists' => 'Toko tidak ditemukan.',
            'customer_id.exists' => 'Data laporan tidak ditemukan. Customer tidak ditemukan.',
            'product_id.exists' => 'Data laporan tidak ditemukan. Produk tidak ditemukan.',
            'order_status.in' => 'Status pesanan tidak valid.',
            'payment_status.in' => 'Status pembayaran tidak valid.',
            'invoice_number.max' => 'Nomor invoice tidak valid.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            // Verify if there's any data to export
            $query = Order::whereBetween('created_at', [$this->start_date . ' 00:00:00', $this->end_date . ' 23:59:59']);
            
            if ($this->store_id) $query->where('store_id', $this->store_id);
            if ($this->order_status) $query->where('status', $this->order_status);
            if ($this->payment_status) $query->where('payment_status', $this->payment_status);

            if ($this->invoice_number || $this->customer_id) {
                $invoiceNumber = $this->invoice_number;
                $customerId = $this->customer_id;
                $query->whereHas('invoice', function ($q) use ($invoiceNumber, $customerId) {
                    if ($invoiceNumber) {
                        $q->where(function($sub) use ($invoiceNumber) {
                            $sub->where('invoice_number', 'like', "%{$invoiceNumber}%")
                                ->orWhere('midtrans_order_id', 'like', "%{$invoiceNumber}%");
                        });
                    }
                    if ($customerId) {
                        $q->where('user_id', $customerId);
                    }
                });
            }

            if ($this->product_id) {
                $productId = $this->product_id;
                $query->whereHas('orderItems', function ($q) use ($productId) {
                    $q->where('product_id', $productId);
                });
            }

            if ($query->count() === 0) {
                $validator->errors()->add('export', 'Tidak ada data yang dapat diekspor.');
            }
        });
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal Mengunduh Laporan',
            'list' => [$validator->errors()->first()]
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
