<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Traits\HasSeoMeta;

class Competition extends Model
{
    use HasSeoMeta;
    protected $fillable = [
        'uuid',
        'admin_id',
        'organizer_id',
        'category_id',
        'title',
        'slug',
        'description',
        'theme',
        'hero_image',
        'hero_image_credit_name',
        'hero_image_credit_url',
        'banner_image',
        'banner_image_credit_name',
        'banner_image_credit_url',
        'cover_image',
        'cover_image_credit_name',
        'cover_image_credit_url',
        'submission_deadline',
        'start_date',
        'end_date',
        'voting_start_at',
        'voting_end_at',
        'voting_start_date',
        'voting_end_date',
        'judging_start_at',
        'judging_end_at',
        'judging_deadline',
        'results_announcement_date',
        'announcement_date',
        'status',
        'mode',
        'entry_type',
        'series_min_images',
        'series_max_images',
        'allow_public_voting',
        'voting_enabled',
        'allow_judge_scoring',
        'vote_weight',
        'judge_weight',
        'show_judge_reactions',
        'allow_watermark',
        'require_watermark',
        'participation_fee',
        'is_paid_competition',
        'max_submissions_per_user',
        'min_submissions_to_proceed',
        'district_battle_enabled',
        'rules',
        'terms_and_conditions',
        'prizes',
        'total_prize_pool',
        'number_of_winners',
        'is_public',
        'is_featured',
        'featured_until',
        'total_submissions',
        'total_votes',
        'results_published',
        'published_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submission_deadline' => 'datetime',
        'voting_start_at' => 'datetime',
        'voting_end_at' => 'datetime',
        'voting_start_date' => 'datetime',
        'voting_end_date' => 'datetime',
        'judging_start_at' => 'datetime',
        'judging_end_at' => 'datetime',
        'judging_deadline' => 'date',
        'results_announcement_date' => 'date',
        'announcement_date' => 'date',
        'featured_until' => 'datetime',
        'published_at' => 'datetime',
        'allow_public_voting' => 'boolean',
        'voting_enabled' => 'boolean',
        'allow_judge_scoring' => 'boolean',
        'vote_weight' => 'decimal:2',
        'judge_weight' => 'decimal:2',
        'show_judge_reactions' => 'boolean',
        'allow_watermark' => 'boolean',
        'require_watermark' => 'boolean',
        'is_paid_competition' => 'boolean',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'results_published' => 'boolean',
        'district_battle_enabled' => 'boolean',
        'participation_fee' => 'decimal:2',
        'total_prize_pool' => 'decimal:2',
        'prizes' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($competition) {
            if (empty($competition->uuid)) {
                $competition->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * SEO helpers for auto-generated meta
     */
    protected function getSeoTitle(): string
    {
        return trim("{$this->title} | Photography Competition | Photographer SB");
    }

    protected function getSeoDescription(): string
    {
        $description = trim(strip_tags($this->description ?? ''));
        return $description !== '' ? $description : 'Join this photography competition on Photographer SB.';
    }

    protected function getSeoCanonicalUrl(string $slug): string
    {
        return url("/competitions/{$slug}");
    }

    protected function getSeoImage(): ?string
    {
        return $this->cover_image ?? $this->banner_image ?? $this->hero_image;
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class, 'organizer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(CompetitionSubmission::class);
    }

    public function entryFees(): HasMany
    {
        return $this->hasMany(CompetitionEntryFee::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CompetitionPayment::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(CompetitionVote::class);
    }

    public function prizes(): HasMany
    {
        return $this->hasMany(CompetitionPrize::class);
    }

    public function prizeWinners(): HasMany
    {
        return $this->hasMany(CompetitionPrizeWinner::class);
    }

    public function shareFrameTemplates(): HasMany
    {
        return $this->hasMany(CompetitionShareFrameTemplate::class);
    }

    public function activeShareFrameTemplate(): HasOne
    {
        return $this->hasOne(CompetitionShareFrameTemplate::class)->where('is_active', true);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(CompetitionCategory::class);
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(CompetitionSponsor::class);
    }

    public function sponsorRecords(): BelongsToMany
    {
        return $this->belongsToMany(Sponsor::class, 'competition_sponsors', 'competition_id', 'sponsor_id')
            ->withPivot([
                'name',
                'logo_url',
                'website_url',
                'description',
                'tier',
                'contribution_amount',
                'display_order',
                'is_active',
            ])
            ->withTimestamps();
    }

    public function scores(): HasMany
    {
        return $this->hasMany(CompetitionScore::class);
    }

    public function mentors(): BelongsToMany
    {
        return $this->belongsToMany(Mentor::class, 'competition_mentor')
            ->withPivot(['role_type', 'note', 'sort_order'])
            ->withTimestamps()
            ->orderBy('competition_mentor.sort_order');
    }

    public function judges(): HasMany
    {
        return $this->hasMany(CompetitionJudge::class);
    }

    public function judgeUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'competition_judges', 'competition_id', 'judge_id')
            ->withPivot(['role', 'bio', 'expertise', 'is_active', 'sort_order', 'assigned_at'])
            ->withTimestamps()
            ->orderBy('competition_judges.sort_order');
    }

    public function judgeProfiles(): BelongsToMany
    {
        return $this->belongsToMany(Judge::class, 'competition_judges', 'competition_id', 'judge_profile_id')
            ->withPivot(['role', 'bio', 'expertise', 'is_active', 'sort_order', 'assigned_at'])
            ->withTimestamps()
            ->orderBy('competition_judges.sort_order');
    }

    public function scoringCriteria(): HasMany
    {
        return $this->hasMany(ScoringCriterion::class)->ordered();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereIn('status', ['published', 'active']);
    }

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query->published()
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', $now);
            })
            ->where(function (Builder $q) use ($now) {
                $q->whereNull('submission_deadline')->orWhere('submission_deadline', '>=', $now);
            })
            ->where(function (Builder $q) {
                $q->whereNull('results_published')->orWhere('results_published', false);
            });
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        $now = now();

        return $query->published()
            ->whereNotNull('published_at')
            ->where('published_at', '>', $now);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        $now = now();

        return $query->published()->where(function (Builder $q) use ($now) {
            $q->where('status', 'completed')
                ->orWhere('results_published', true)
                ->orWhere(function (Builder $inner) use ($now) {
                    $inner->whereNotNull('submission_deadline')
                        ->where('submission_deadline', '<', $now);
                });
        });
    }
}
