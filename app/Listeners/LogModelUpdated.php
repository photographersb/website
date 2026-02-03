<?php

namespace App\Listeners;

use App\Services\ActivityLogService;

class LogModelUpdated
{
    /**
     * Handle model updated events
     */
    public function handle($event): void
    {
        if ($event instanceof \Illuminate\Database\Eloquent\Events\Updated) {
            $changes = $event->model->getChanges();
            if (!empty($changes)) {
                ActivityLogService::logUpdated($event->model, $changes);
            }
        }
    }
}
