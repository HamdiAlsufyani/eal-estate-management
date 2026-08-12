<?php

namespace App\Listeners;

use App\Events\PropertyCreated;
use App\Models\User;
use App\Notifications\PropertySubmittedNotification;
use Spatie\Permission\Models\Permission;

class SendPropertySubmittedNotification
{
    public function handle(PropertyCreated $event): void
    {
        if (! Permission::where('name', 'properties.approve')->where('guard_name', 'web')->exists()) {
            return;
        }

        $recipients = User::query()->permission('properties.approve')->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new PropertySubmittedNotification($event->property));
        }
    }
}
