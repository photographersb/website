<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminSettingsController extends Controller
{
    use ApiResponse;
    /**
     * Get all system settings
     */
    public function index()
    {
        try {
            $settings = DB::table('settings')->get();
            
            // Convert to key-value pairs
            $settingsData = [];
            foreach ($settings as $setting) {
                $settingsData[$setting->key] = $setting->value;
            }

            return $this->success($settingsData, 'Settings retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to fetch settings: ' . $e->getMessage());
            return $this->error('Failed to fetch settings', 500);
        }
    }

    /**
     * Update a setting
     */
    public function update(Request $request, $key)
    {
        $validated = $request->validate([
            // Keep key mandatory while allowing explicit null to clear a setting value.
            'value' => 'present'
        ]);

        $normalizedValue = $this->normalizeSettingValue($validated['value']);
        $dataType = $this->inferDataType($validated['value']);
        $group = $this->inferGroup($key);

        try {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $normalizedValue,
                    'group' => $group,
                    'data_type' => $dataType,
                    'updated_at' => now()
                ]
            );

            // Clear cache
            Cache::forget("setting_{$key}");

            Log::info("Setting '{$key}' updated by admin " . Auth::id());

            return $this->success(['key' => $key, 'value' => $validated['value']], 'Setting updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update setting: ' . $e->getMessage());
            return $this->error('Failed to update setting', 500);
        }
    }

    /**
     * Bulk update settings
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array|min:1|max:500'
        ]);

        $rawSettings = $validated['settings'];
        $normalizedSettings = [];

        if (Arr::isList($rawSettings)) {
            $normalizedSettings = $rawSettings;
        } else {
            foreach ($rawSettings as $key => $value) {
                $normalizedSettings[] = ['key' => $key, 'value' => $value];
            }
        }

        $validatedSettings = Validator::make([
            'settings' => $normalizedSettings,
        ], [
            'settings' => 'required|array|min:1|max:500',
            'settings.*.key' => 'required|string|max:255',
            'settings.*.value' => 'nullable',
            'settings.*.group' => 'nullable|string|max:50',
            'settings.*.data_type' => 'nullable|string|max:20',
            'settings.*.description' => 'nullable|string|max:1000',
            'settings.*.is_public' => 'nullable|boolean',
        ])->validate();

        try {
            foreach ($validatedSettings['settings'] as $setting) {
                $normalizedValue = $this->normalizeSettingValue($setting['value'] ?? null);
                $dataType = $setting['data_type'] ?? $this->inferDataType($setting['value'] ?? null);
                $group = $setting['group'] ?? $this->inferGroup($setting['key']);
                $description = $setting['description'] ?? null;
                $isPublic = array_key_exists('is_public', $setting) ? (bool) $setting['is_public'] : false;

                DB::table('settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    [
                        'value' => $normalizedValue,
                        'group' => $group,
                        'data_type' => $dataType,
                        'description' => $description,
                        'is_public' => $isPublic,
                        'updated_at' => now()
                    ]
                );
                Cache::forget("setting_{$setting['key']}");
            }

            Log::info("Bulk settings updated by admin " . Auth::id());

            return $this->success([], 'Settings updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to bulk update settings: ' . $e->getMessage());
            return $this->error('Failed to update settings', 500);
        }
    }

    /**
     * Get specific setting category
     */
    public function getCategory($category)
    {
        try {
            $settings = DB::table('settings')
                ->where('key', 'LIKE', "{$category}.%")
                ->get();

            $settingsData = [];
            foreach ($settings as $setting) {
                $key = str_replace("{$category}.", '', $setting->key);
                $settingsData[$key] = $setting->value;
            }

            return $this->success($settingsData, 'Category settings retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to fetch category settings: ' . $e->getMessage());
            return $this->error('Failed to fetch settings', 500);
        }
    }

    /**
     * Reset settings to default
     */
    public function reset()
    {
        try {
            // Define default settings
            $defaults = [
                ['key' => 'site.name', 'value' => 'Photographer SB'],
                ['key' => 'site.description', 'value' => 'Professional Photography Platform'],
                ['key' => 'site.email', 'value' => 'info@photographersb.com'],
                ['key' => 'site.phone', 'value' => '+880 1234567890'],
                ['key' => 'booking.commission_rate', 'value' => '15'],
                ['key' => 'payment.enable_ssl', 'value' => 'true'],
                ['key' => 'notification.email_enabled', 'value' => 'true'],
                ['key' => 'notification.sms_enabled', 'value' => 'false'],
            ];

            foreach ($defaults as $setting) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    [
                        'value' => $setting['value'],
                        'updated_at' => now()
                    ]
                );
            }

            Cache::flush();

            Log::info("Settings reset to defaults by admin " . Auth::id());

            return $this->success([], 'Settings reset to defaults');
        } catch (\Exception $e) {
            Log::error('Failed to reset settings: ' . $e->getMessage());
            return $this->error('Failed to reset settings', 500);
        }
    }

    /**
     * Upload SEO OG image and store settings key
     */
    public function uploadSeoOgImage(Request $request)
    {
        $request->validate([
            'og_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if (!$request->hasFile('og_image')) {
            return $this->error('No file uploaded', 400);
        }

        $file = $request->file('og_image');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('seo', $filename, 'public');
        $url = Storage::url($path);

        try {
            DB::table('settings')->updateOrInsert(
                ['key' => 'seo.og_image_url'],
                [
                    'value' => $url,
                    'group' => 'seo',
                    'data_type' => 'string',
                    'description' => 'Default OG image for social sharing',
                    'is_public' => false,
                    'updated_at' => now()
                ]
            );
            Cache::forget('setting_seo.og_image_url');
        } catch (\Exception $e) {
            Log::error('Failed to save SEO OG image setting: ' . $e->getMessage());
            return $this->error('Failed to store OG image setting', 500);
        }

        return $this->success([
            'url' => $url,
            'path' => $path,
        ], 'OG image uploaded successfully');
    }

    /**
     * Get settings change history
     */
    public function getChanges(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $page = $request->input('page', 1);

            // Mock data for now - you can implement actual change tracking later
            $changes = [];
            
            return $this->success($changes, 'Settings changes retrieved successfully', 200, [
                'total' => 0,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => 1
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch settings changes: ' . $e->getMessage());
            return $this->error('Failed to fetch settings changes', 500);
        }
    }

    private function normalizeSettingValue($value): ?string
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return null;
        }

        return (string) $value;
    }

    private function inferDataType($value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_int($value)) {
            return 'integer';
        }

        if (is_float($value)) {
            return 'float';
        }

        if (is_array($value) || is_object($value)) {
            return 'json';
        }

        return 'string';
    }

    private function inferGroup(string $key): string
    {
        $prefix = explode('.', $key)[0] ?? '';
        if ($prefix === '') {
            return 'general';
        }

        return $prefix;
    }
}
