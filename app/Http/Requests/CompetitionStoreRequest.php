<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Judge;
use App\Models\User;

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
            'mode' => 'nullable|in:open,pro,student,district_battle',
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where('is_active', 1),
            ],
            'start_date' => 'nullable|date',
            'submission_deadline' => $isDraft ? ['nullable', 'date'] : ['required', 'date', 'after:now'],
            'end_date' => 'nullable|date|after_or_equal:submission_deadline',
            'voting_start_at' => 'nullable|date',
            'voting_end_at' => 'nullable|date|after:voting_start_at',
            'voting_start_date' => 'nullable|date',
            'voting_end_date' => 'nullable|date|after:voting_start_date',
            'judging_start_at' => 'nullable|date',
            'judging_end_at' => 'nullable|date|after:judging_start_at',
            'judging_deadline' => 'nullable|date|after_or_equal:judging_end_at',
            'results_announcement_date' => 'nullable|date',
            'announcement_date' => 'nullable|date|after_or_equal:results_announcement_date',
            'total_prize_pool' => [Rule::requiredIf(!$isDraft), 'nullable', 'numeric', 'min:0'],
            'max_submissions_per_user' => [Rule::requiredIf(!$isDraft), 'nullable', 'integer', 'min:1'],
            'min_submissions_to_proceed' => 'nullable|integer|min:1',
            'number_of_winners' => [Rule::requiredIf(!$isDraft), 'nullable', 'integer', 'min:1'],
            'participation_fee' => 'nullable|numeric|min:0',
            'rules' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'banner_image' => 'nullable|url',
            'banner_image_credit_name' => 'nullable|string|max:255',
            'banner_image_credit_url' => 'nullable|url|max:255',
            'hero_image' => 'nullable|url',
            'hero_image_credit_name' => 'nullable|string|max:255',
            'hero_image_credit_url' => 'nullable|url|max:255',
            'cover_image' => 'nullable|url',
            'cover_image_credit_name' => 'nullable|string|max:255',
            'cover_image_credit_url' => 'nullable|url|max:255',
            'entry_type' => 'nullable|in:single,series,both',
            'series_min_images' => 'nullable|integer|min:1',
            'series_max_images' => 'nullable|integer|min:1|gte:series_min_images',
            'is_featured' => 'boolean',
            'featured_until' => 'nullable|date|after:now',
            'allow_public_voting' => 'boolean',
            'voting_enabled' => 'boolean',
            'allow_judge_scoring' => 'boolean',
            'vote_weight' => 'nullable|numeric|min:0|max:1',
            'judge_weight' => 'nullable|numeric|min:0|max:1',
            'show_judge_reactions' => 'boolean',
            'allow_watermark' => 'boolean',
            'require_watermark' => 'boolean',
            'district_battle_enabled' => 'boolean',
            'status' => 'required|in:draft,active,judging,completed,cancelled,archived',
            'prizes' => 'nullable|array',
            'prizes.*.position' => 'required_with:prizes|string|max:255',
            'prizes.*.amount' => 'required_with:prizes|numeric|min:0',
            'prizes.*.award_type' => 'nullable|in:global,district,people_choice,special',
            'prizes.*.prize_type' => 'nullable|in:cash,trophy,gift,certificate,sponsor_product,featured_boost',
            'prizes.*.sponsor_id' => 'nullable|integer|exists:sponsors,id',
            'prizes.*.sort_order' => 'nullable|integer|min:0',
            'sponsor_ids' => ['nullable', 'array'],
            'sponsor_ids.*' => ['integer', 'exists:sponsors,id'],
            'sponsors' => ['nullable', 'array'],
            'sponsors.*.sponsor_id' => ['required_with:sponsors', 'integer', 'exists:sponsors,id'],
            'sponsors.*.tier' => ['nullable', 'in:title,gold,silver,bronze,support,platinum'],
            'sponsors.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'sponsors.*.sponsored_amount' => ['nullable', 'numeric', 'min:0'],
            'entry_fees' => ['nullable', 'array'],
            'entry_fees.*.user_type' => ['required_with:entry_fees', 'in:guest,registered,verified,student'],
            'entry_fees.*.fee_amount' => ['required_with:entry_fees', 'numeric', 'min:0'],
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
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $status = $this->input('status');
            if ($status === 'draft') {
                return;
            }

            $voteWeight = $this->input('vote_weight');
            $judgeWeight = $this->input('judge_weight');

            if ($voteWeight === null || $judgeWeight === null) {
                $validator->errors()->add('vote_weight', 'Public vote and judge score percentages are required.');
                return;
            }

            $vote = (float) $voteWeight;
            $judge = (float) $judgeWeight;
            if (abs(($vote + $judge) - 1) > 0.01) {
                $validator->errors()->add('vote_weight', 'Public vote and judge score must total 100%.');
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->status === 'published') {
            $this->merge(['status' => 'active']);
        }

        $this->merge([
            'judge_ids' => $this->withDefaultJudge($this->input('judge_ids')),
        ]);

        if (!$this->has('slug') || empty($this->slug)) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($this->title),
            ]);
        }

        if ($this->filled('end_date') && is_string($this->end_date)) {
            $endDate = trim($this->end_date);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                $this->merge([
                    'end_date' => Carbon::parse($endDate)->endOfDay()->toDateTimeString(),
                ]);
            }
        }

        // Convert boolean strings to actual booleans
        $this->merge([
            'is_featured' => filter_var($this->is_featured, FILTER_VALIDATE_BOOLEAN),
            'allow_public_voting' => filter_var($this->allow_public_voting, FILTER_VALIDATE_BOOLEAN),
            'voting_enabled' => filter_var($this->voting_enabled, FILTER_VALIDATE_BOOLEAN),
            'allow_judge_scoring' => filter_var($this->allow_judge_scoring, FILTER_VALIDATE_BOOLEAN),
            'show_judge_reactions' => filter_var($this->show_judge_reactions, FILTER_VALIDATE_BOOLEAN),
            'allow_watermark' => filter_var($this->allow_watermark, FILTER_VALIDATE_BOOLEAN),
            'require_watermark' => filter_var($this->require_watermark, FILTER_VALIDATE_BOOLEAN),
            'district_battle_enabled' => filter_var($this->district_battle_enabled, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    private function withDefaultJudge($judgeIds): array
    {
        $ids = is_array($judgeIds) ? $judgeIds : [];
        $defaultEmail = 'mahidulislamnakib@gmail.com';

        $user = User::where('email', $defaultEmail)->first();
        if (!$user) {
            return $ids;
        }

        $judgeProfile = Judge::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name ?? 'Super Admin Judge',
                'email' => $user->email,
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        if (!in_array($judgeProfile->id, $ids, true)) {
            $ids[] = $judgeProfile->id;
        }

        return $ids;
    }
}
