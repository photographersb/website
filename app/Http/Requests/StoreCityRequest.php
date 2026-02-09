<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255', 'unique:locations,name,' . ($id ?? 'NULL')],
            'slug' => ['required', 'string', 'max:255', 'unique:locations,slug,' . ($id ?? 'NULL')],
            'type' => ['required', 'in:division,district,upazila'],
            'parent_id' => ['nullable', 'exists:locations,id'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Location name is required',
            'name.unique' => 'A location with this name already exists',
            'slug.required' => 'Location slug is required',
            'slug.unique' => 'A location with this slug already exists',
            'type.required' => 'Location type is required',
            'type.in' => 'Location type must be division, district, or upazila',
        ];
    }
}
