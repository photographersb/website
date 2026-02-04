<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin');
    }

    public function rules(): array
    {
        return [
            'admin_note' => 'nullable|string|max:500'
        ];
    }

    public function messages(): array
    {
        return [
            'admin_note.max' => 'Admin note must not exceed 500 characters'
        ];
    }
}
