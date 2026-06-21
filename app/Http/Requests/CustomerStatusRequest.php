<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'is_active' => [
                'required',
                'boolean',
                function ($attribute, $value, $fail) {
                    $customerId = $this->route('customer');
                    $customer = \App\Models\User::where('role', 'customer')->find($customerId);

                    if (!$customer) {
                        $fail('Data customer tidak ditemukan.');
                        return;
                    }

                    if ($value == 0 && $customer->is_active == 0) {
                        $fail('Akun customer sudah dinonaktifkan.');
                    }

                    if ($value == 1 && $customer->is_active == 1) {
                        $fail('Akun customer sudah aktif.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'is_active.required' => 'Status aktif wajib diisi.',
            'is_active.boolean' => 'Data customer tidak valid.',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Custom format for error toast based on layouts/app.blade.php expected format
        $response = redirect()->back()->with('error', [
            'title' => 'Gagal Memproses Permintaan',
            'list' => [$validator->errors()->first()]
        ]);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
