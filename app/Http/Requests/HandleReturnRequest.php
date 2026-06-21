<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HandleReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'return_status' => 'required|in:approved,rejected',
        ];
    }

    public function messages(): array
    {
        return [
            'return_status.required' => 'Status pengembalian tidak valid.',
            'return_status.in' => 'Status pengembalian tidak valid.',
        ];
    }
}
