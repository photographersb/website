<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'photographer';
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize sample_images to array format
        if ($this->has('sample_images')) {
            $sampleImages = $this->input('sample_images');
            
            // If it's a string (single URL), convert to array
            if (is_string($sampleImages)) {
                $this->merge([
                    'sample_images' => [$sampleImages]
                ]);
            }
            // If it's already an array but empty string, set to empty array
            elseif (is_array($sampleImages) && count($sampleImages) === 1 && empty($sampleImages[0])) {
                $this->merge([
                    'sample_images' => []
                ]);
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'string', 'max:100'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_unit' => ['required', 'in:hour,day,event'],
            'duration_value' => ['required', 'integer', 'min:1'],
            'edited_photos' => ['nullable', 'integer', 'min:0'],
            'raw_photos' => ['nullable', 'integer', 'min:0'],
            'delivery_days' => ['required', 'integer', 'min:1', 'max:365'],
            
            // Flexible sample_images validation
            'sample_images' => ['nullable', 'array', 'max:10'],
            'sample_images.*' => ['nullable', 'string', 'url', 'max:500'],
            
            // Or uploaded files
            'uploaded_images' => ['nullable', 'array', 'max:10'],
            'uploaded_images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'], // 5MB
            
            'cover_image' => ['nullable', 'string', 'url', 'max:500'],
            'includes' => ['nullable', 'array'],
            'includes.*' => ['string', 'max:255'],
            'excludes' => ['nullable', 'array'],
            'excludes.*' => ['string', 'max:255'],
            'add_ons' => ['nullable', 'array'],
            'travel_cost_type' => ['nullable', 'in:fixed,per_km,included,not_included'],
            'travel_cost_value' => ['nullable', 'numeric', 'min:0'],
            'advance_booking_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'is_active' => ['boolean'],
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
            'name.required' => 'Please enter a package name.',
            'name.min' => 'Package name must be at least 3 characters long.',
            'category.required' => 'Please select a category.',
            'price.required' => 'Please enter a price for this package.',
            'price.min' => 'Price must be greater than or equal to 0.',
            'duration_unit.required' => 'Please select a duration unit.',
            'duration_value.required' => 'Please enter a duration value.',
            'delivery_days.required' => 'Please specify delivery days.',
            'sample_images.*.url' => 'Each sample image must be a valid URL.',
            'sample_images.max' => 'You can add maximum 10 sample images.',
            'uploaded_images.*.image' => 'Uploaded files must be images.',
            'uploaded_images.*.mimes' => 'Images must be in JPEG, JPG, PNG, or WebP format.',
            'uploaded_images.*.max' => 'Each image must not exceed 5MB.',
        ];
    }
}
