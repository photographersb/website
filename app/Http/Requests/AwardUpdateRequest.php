<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AwardUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $award = $this->route('award');
        return auth()->check() 
            && auth()->user()->role === 'photographer'
            && $award->photographer_id === auth()->user()->photographer->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentYear = date('Y');
        
        return [
            'title' => ['required', 'string', 'max:255', 'min:3'],
            'organization' => ['nullable', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1950', 'max:' . ($currentYear + 1)],
            'description' => ['nullable', 'string', 'max:1000'],
            'certificate_url' => ['nullable', 'string', 'url', 'max:500'],
            'type' => ['required', 'in:award,achievement,recognition,certification'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            
            // Handle file upload
            'certificate_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png,pdf', 'max:5120'], // 5MB
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please enter the award title.',
            'title.min' => 'Award title must be at least 3 characters long.',
            'year.required' => 'Please select the year you received this award.',
            'year.min' => 'Year must be at least 1950.',
            'year.max' => 'Year cannot be in the future.',
            'type.required' => 'Please select the award type.',
            'type.in' => 'Invalid award type selected.',
            'certificate_file.mimes' => 'Certificate must be in JPEG, PNG, or PDF format.',
            'certificate_file.max' => 'Certificate file must not exceed 5MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'award title',
            'organization' => 'organization name',
            'year' => 'award year',
            'type' => 'award type',
        ];
    }
}
