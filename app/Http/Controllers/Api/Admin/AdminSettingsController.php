<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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
            'value' => 'required|string'
        ]);

        try {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $validated['value'],
                    'updated_at' => now()
                ]
            );

            // Clear cache
            Cache::forget("setting_{$key}");

            Log::info("Setting '{$key}' updated by admin " . auth()->user()->id);

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
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required|string'
        ]);

        try {
            foreach ($validated['settings'] as $setting) {
                DB::table('settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    [
                        'value' => $setting['value'],
                        'updated_at' => now()
                    ]
                );
                Cache::forget("setting_{$setting['key']}");
            }

            Log::info("Bulk settings updated by admin " . auth()->user()->id);

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

            Log::info("Settings reset to defaults by admin " . auth()->user()->id);

            return $this->success([], 'Settings reset to defaults');
        } catch (\Exception $e) {
            Log::error('Failed to reset settings: ' . $e->getMessage());
            return $this->error('Failed to reset settings', 500);
        }
    }
}
