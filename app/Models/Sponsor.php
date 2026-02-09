<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'logo_credit_name',
        'logo_credit_url',
        'website',
        'website_url',
        'description',
        'status',
        'is_active',
        'display_order',
        'start_date',
        'end_date',
        'is_featured',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function activities()
    {
        return $this->hasMany(SponsorActivity::class);
    }

    public function competitionSponsors()
    {
        return $this->hasMany(CompetitionSponsor::class);
    }

    public function getLogoAttribute($value)
    {
        if (!$value) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://', '/storage/'])) {
            return $value;
        }

        return Storage::url($value);
    }
}
