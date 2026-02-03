<?php

namespace App\Listeners;

use App\Services\ActivityLogService;

class LogModelDeleted
{
    /**
     * Handle model deleted events
     */
    public function handle($event): void
    {
        if ($event instanceof \Illuminate\Database\Eloquent\Events\Deleted) {
            ActivityLogService::logDeleted($event->model);
        }
    }
}
