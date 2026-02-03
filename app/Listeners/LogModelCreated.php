<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Database\Events\QueryExecuted;

class LogModelCreated
{
    /**
     * Handle model created events
     */
    public function handle($event): void
    {
        if ($event instanceof \Illuminate\Database\Eloquent\Events\Created) {
            ActivityLogService::logCreated($event->model);
        }
    }
}
