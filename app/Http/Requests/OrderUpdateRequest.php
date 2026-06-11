<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        // Mendukung validasi status dan shipping_type secara terpisah sesuai kebutuhan
        $rules = [];
        
        if ($this->has('status')) {
            $allowed = implode(',', [
                Order::STATUS_PENDING,
                Order::STATUS_PERLU_DIPROSES,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPING,
                Order::STATUS_COMPLETED
            ]);
            $rules['status'] = "required|string|in:{$allowed}";
        }

        if ($this->has('shipping_type')) {
            $rules['shipping_type'] = 'required|string|in:reguler,cargo';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status pesanan wajib dipilih.',
            'status.string' => 'Status pesanan harus berupa teks.',
            'status.in' => 'Status pesanan yang Anda pilih tidak valid.',
            
            'shipping_type.required' => 'Jenis pengiriman wajib dipilih.',
            'shipping_type.string' => 'Jenis pengiriman harus berupa teks.',
            'shipping_type.in' => 'Jenis pengiriman yang dipilih tidak tersedia (hanya Reguler atau Cargo).',
        ];
    }
}
