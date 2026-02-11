<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $type = $this->input('type');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations', 'name')
                    ->where(fn ($query) => $query->where('type', $type))
                    ->ignore($id),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations', 'slug')->ignore($id),
            ],
            'type' => ['required', 'in:division,district,upazila'],
            'parent_id' => [
                Rule::requiredIf(in_array($type, ['district', 'upazila'], true)),
                'nullable',
                'exists:locations,id',
            ],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $type = $this->input('type');
            $parentId = $this->input('parent_id');

            if ($type === 'division' && $parentId) {
                $validator->errors()->add('parent_id', 'Divisions cannot have a parent location.');
                return;
            }

            if (!$parentId) {
                return;
            }

            $parent = Location::select('id', 'type')->find($parentId);
            if (!$parent) {
                return;
            }

            if ($type === 'district' && $parent->type !== 'division') {
                $validator->errors()->add('parent_id', 'Districts must have a division as parent.');
            }

            if ($type === 'upazila' && $parent->type !== 'district') {
                $validator->errors()->add('parent_id', 'Upazilas must have a district as parent.');
            }
        });
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
            'slug.unique' => 'Location slug must be unique across all locations',
            'type.required' => 'Location type is required',
            'type.in' => 'Location type must be division, district, or upazila',
        ];
    }
}
