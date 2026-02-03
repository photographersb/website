<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompetitionRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:competitions,slug'],
            'description' => ['required', 'string', 'min:20'],
            'category_id' => ['required', 'exists:competition_categories,id'],
            'status' => ['required', 'in:draft,upcoming,active,closed,completed,archived'],
            'registration_deadline' => ['required', 'date', 'after:now'],
            'start_date' => ['required', 'date', 'after:registration_deadline'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'max_participants' => ['required', 'integer', 'min:1'],
            'entry_fee' => ['nullable', 'numeric', 'min:0'],
            'prize_pool' => ['nullable', 'numeric', 'min:0'],
            'terms_and_conditions' => ['nullable', 'string'],
            'rules' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Competition title is required',
            'start_date.after' => 'Start date must be after registration deadline',
            'end_date.after' => 'End date must be after start date',
            'max_participants.min' => 'Must allow at least 1 participant',
        ];
    }
}
