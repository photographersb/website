<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Competition extends Model
{
    protected $fillable = [
        'uuid',
        'admin_id',
        'organizer_id',
        'title',
        'slug',
        'description',
        'theme',
        'hero_image',
        'banner_image',
        'submission_deadline',
        'voting_start_at',
        'voting_end_at',
        'judging_start_at',
        'judging_end_at',
        'results_announcement_date',
        'status',
        'allow_public_voting',
        'allow_judge_scoring',
        'allow_watermark',
        'require_watermark',
        'participation_fee',
        'is_paid_competition',
        'max_submissions_per_user',
        'min_submissions_to_proceed',
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
        'submission_deadline' => 'datetime',
        'voting_start_at' => 'datetime',
        'voting_end_at' => 'datetime',
        'judging_start_at' => 'datetime',
        'judging_end_at' => 'datetime',
        'results_announcement_date' => 'date',
        'featured_until' => 'datetime',
        'published_at' => 'datetime',
        'allow_public_voting' => 'boolean',
        'allow_judge_scoring' => 'boolean',
        'allow_watermark' => 'boolean',
        'require_watermark' => 'boolean',
        'is_paid_competition' => 'boolean',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'results_published' => 'boolean',
        'participation_fee' => 'decimal:2',
        'total_prize_pool' => 'decimal:2',
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

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Photographer::class, 'organizer_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(CompetitionSubmission::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(CompetitionVote::class);
    }

    public function prizes(): HasMany
    {
        return $this->hasMany(CompetitionPrize::class);
    }

    public function sponsors(): HasMany
    {
        return $this->hasMany(CompetitionSponsor::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
