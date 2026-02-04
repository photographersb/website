<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin');
    }

    public function rules(): array
    {
        return [
            'admin_note' => 'required|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'admin_note.required' => 'Please provide a rejection reason',
            'admin_note.max' => 'Rejection reason must not exceed 1000 characters'
        ];
    }
}
