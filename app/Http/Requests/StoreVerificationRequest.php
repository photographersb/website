<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVerificationRequest extends FormRequest
{
        public function authorize(): bool
        {
            return auth()->check() && auth()->user()->role === 'photographer';
        }

        public function rules(): array
        {
            return [
                'type' => 'required|in:phone,nid,business',
                'full_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'nid_number' => 'nullable|string|max:50',
                'business_name' => 'nullable|string|max:255',
                'document_front_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'document_back_path' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
                'selfie_path' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
                'note' => 'nullable|string|max:1000'
            ];
        }

        public function messages(): array
        {
            return [
                'type.required' => 'Please select a verification type',
                'full_name.required' => 'Full name is required',
                'phone.required' => 'Phone number is required',
                'document_front_path.mimes' => 'Document must be JPG, PNG, or PDF',
                'document_front_path.max' => 'Document must not exceed 10MB'
            ];
        }
}
