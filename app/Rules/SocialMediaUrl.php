<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SocialMediaUrl implements ValidationRule
{
    private string $platform;
    private array $patterns = [
        'facebook' => [
            '#^https?://(?:www\.)?facebook\.com/[\w\-\.]+(?:\?.*)?$#',
            '#^https?://(?:www\.)?m\.facebook\.com/[\w\-\.]+(?:\?.*)?$#',
        ],
        'instagram' => [
            '#^https?://(?:www\.)?instagram\.com/[\w\-\.]+(?:\?.*)?$#',
        ],
        'twitter' => [
            '#^https?://(?:www\.)?(?:twitter\.com|x\.com)/[\w\-\.]+(?:\?.*)?$#',
        ],
        'linkedin' => [
            '#^https?://(?:www\.)?linkedin\.com/(?:in|company)/[\w\-\.]+(?:\?.*)?$#',
        ],
        'youtube' => [
            '#^https?://(?:www\.)?youtube\.com/(?:c|user|channel|@)/[\w\-\.]+(?:\?.*)?$#',
            '#^https?://(?:www\.)?youtube\.com/@[\w\-\.]+(?:\?.*)?$#',
        ],
    ];

    public function __construct(string $platform)
    {
        $this->platform = strtolower($platform);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value || !is_string($value)) {
            return; // Allow null/empty values as they're nullable
        }

        if (!isset($this->patterns[$this->platform])) {
            $fail("Invalid platform: {$this->platform}");
            return;
        }

        $isValid = false;
        foreach ($this->patterns[$this->platform] as $pattern) {
            if (preg_match($pattern, $value)) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            $fail("The {$attribute} must be a valid {$this->platform} URL.");
        }
    }
}
