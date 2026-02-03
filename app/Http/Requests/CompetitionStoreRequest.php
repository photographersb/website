<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompetitionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        
        $user = auth()->user();
        return in_array($user->role, ['admin', 'super_admin', 'moderator']);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isDraft = $this->input('status') === 'draft';

        return [
            'title' => [Rule::requiredIf(!$isDraft), 'nullable', 'string', 'max:255', 'unique:competitions,title'],
            'slug' => 'nullable|string|max:255|unique:competitions,slug',
            'theme' => [Rule::requiredIf(!$isDraft), 'nullable', 'string', 'max:255'],
            'description' => [Rule::requiredIf(!$isDraft), 'nullable', 'string'],
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where('is_active', 1),
            ],
            'submission_deadline' => $isDraft ? ['nullable', 'date'] : ['required', 'date', 'after:now'],
            'voting_start_at' => 'nullable|date',
            'voting_end_at' => 'nullable|date|after:voting_start_at',
            'judging_start_at' => 'nullable|date',
            'judging_end_at' => 'nullable|date|after:judging_start_at',
            'results_announcement_date' => 'nullable|date',
            'total_prize_pool' => [Rule::requiredIf(!$isDraft), 'nullable', 'numeric', 'min:0'],
            'max_submissions_per_user' => [Rule::requiredIf(!$isDraft), 'nullable', 'integer', 'min:1'],
            'min_submissions_to_proceed' => 'nullable|integer|min:1',
            'number_of_winners' => [Rule::requiredIf(!$isDraft), 'nullable', 'integer', 'min:1'],
            'participation_fee' => 'nullable|numeric|min:0',
            'banner_image' => 'nullable|url',
            'hero_image' => 'nullable|url',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date|after:now',
            'allow_public_voting' => 'boolean',
            'allow_judge_scoring' => 'boolean',
            'allow_watermark' => 'boolean',
            'require_watermark' => 'boolean',
            'status' => 'required|in:draft,active,judging,completed,cancelled,archived',
            'prizes' => 'nullable|array',
            'prizes.*.position' => 'required_with:prizes|string|max:255',
            'prizes.*.amount' => 'required_with:prizes|numeric|min:0',
            'sponsor_ids' => ['nullable', 'array'],
            'sponsor_ids.*' => ['integer', 'exists:sponsors,id'],
            'judge_ids' => $isDraft ? ['nullable', 'array'] : ['required', 'array', 'min:1'],
            'judge_ids.*' => ['integer', 'exists:judges,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Competition title is required',
            'title.unique' => 'A competition with this title already exists',
            'submission_deadline.after' => 'Submission deadline must be in the future',
            'voting_end_at.after' => 'Voting end date must be after voting start date',
            'judging_end_at.after' => 'Judging end date must be after judging start date',
            'banner_image.url' => 'Banner image must be a valid URL',
            'hero_image.url' => 'Hero image must be a valid URL',
            'judge_ids.required' => 'At least one judge is required',
            'judge_ids.min' => 'At least one judge is required',
            'judge_ids.*.exists' => 'One or more selected judges do not exist. Please select valid judges.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->status === 'published') {
            $this->merge(['status' => 'active']);
        }

        if (!$this->has('slug') || empty($this->slug)) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title),
            ]);
        }

        // Convert boolean strings to actual booleans
        $this->merge([
            'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN),
            'allow_public_voting' => filter_var($this->allow_public_voting, FILTER_VALIDATE_BOOLEAN),
            'allow_judge_scoring' => filter_var($this->allow_judge_scoring, FILTER_VALIDATE_BOOLEAN),
            'allow_watermark' => filter_var($this->allow_watermark, FILTER_VALIDATE_BOOLEAN),
            'require_watermark' => filter_var($this->require_watermark, FILTER_VALIDATE_BOOLEAN),
        ]);
    }
}
