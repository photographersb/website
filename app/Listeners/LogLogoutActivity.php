<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Logout;

class LogLogoutActivity
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        ActivityLogService::logLogout($event->user->id);
    }
}
