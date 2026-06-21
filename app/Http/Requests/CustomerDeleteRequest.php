<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'id' => [
                function ($attribute, $value, $fail) {
                    $customerId = $this->route('customer');
                    $customer = \App\Models\User::where('role', 'customer')->find($customerId);

                    if (!$customer) {
                        $fail('Customer tidak ditemukan.');
                        return;
                    }
                    
                    // Note: Soft delete behavior - users table typically uses SoftDeletes if implemented
                    if ($customer->trashed && $customer->trashed()) {
                        $fail('Customer sudah dihapus.');
                        return;
                    }

                    if (!in_array($customer->is_active, [0, 1])) {
                        $fail('Data customer tidak valid.');
                    }
                },
            ],
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal Memproses Permintaan',
            'list' => [$validator->errors()->first()]
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
