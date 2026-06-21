<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Order;

class ScanTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        // Only validate if identifier is present in the request (to allow loading the page initially without errors)
        // Wait, if it's a GET request and identifier is empty, we shouldn't show validation errors unless they submitted the form.
        // To detect a form submission vs initial load, we can check if the request has the 'identifier' key.
        if (!$this->has('identifier')) {
            return [];
        }

        return [
            'identifier' => [
                'required',
                'string',
                'min:5',
                'max:50',
                function ($attribute, $value, $fail) {
                    $order = Order::where('midtrans_order_id', $value)
                        ->orWhere('order_number', $value)
                        ->orWhere('tracking_number', $value)
                        ->first();

                    if (!$order) {
                        $fail('Nomor resi tidak ditemukan.');
                        return;
                    }

                    if (in_array($order->status, [Order::STATUS_CANCELLED, Order::STATUS_REFUNDED])) {
                        $fail('Nomor resi sudah tidak aktif.');
                        return;
                    }

                    if (empty($order->tracking_number) && $value !== $order->order_number && $value !== $order->midtrans_order_id) {
                        // Just in case
                        $fail('Nomor resi tidak terhubung dengan pesanan.');
                        return;
                    }
                }
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'Nomor resi wajib diisi.',
            'identifier.string' => 'Nomor resi tidak valid.',
            'identifier.min' => 'Nomor resi minimal 5 karakter.',
            'identifier.max' => 'Nomor resi maksimal 50 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'identifier' => 'Nomor Resi'
        ];
    }
}
