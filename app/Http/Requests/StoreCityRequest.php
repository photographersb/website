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
        $id = $this->route('city')?->id;

        return [
            'name' => ['required', 'string', 'max:255', 'unique:cities,name,' . ($id ?? 'NULL')],
            'slug' => ['required', 'string', 'max:255', 'unique:cities,slug,' . ($id ?? 'NULL')],
            'division' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'City name is required',
            'name.unique' => 'A city with this name already exists',
            'slug.required' => 'City slug is required',
            'slug.unique' => 'A city with this slug already exists',
        ];
    }
}
