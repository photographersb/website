<?php

namespace App\Traits;

use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    /**
     * Boot the trait
     */
    public static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            try {
                ActivityLogService::logCreated($model);
            } catch (\Exception $e) {
                \Log::error('Failed to log activity: ' . $e->getMessage());
            }
        });

        static::updated(function (Model $model) {
            try {
                $changes = $model->getChanges();
                if (!empty($changes)) {
                    ActivityLogService::logUpdated($model, $changes);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to log activity: ' . $e->getMessage());
            }
        });

        static::deleted(function (Model $model) {
            try {
                ActivityLogService::logDeleted($model);
            } catch (\Exception $e) {
                \Log::error('Failed to log activity: ' . $e->getMessage());
            }
        });
    }
}
