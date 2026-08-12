<?php

namespace App\Listeners;

use App\Events\PropertyStatusChanged;
use App\Notifications\PropertyApprovedNotification;
use App\Notifications\PropertyRejectedNotification;

class SendPropertyStatusNotification
{
    public function handle(PropertyStatusChanged $event): void
    {
        $owner = $event->property->user;

        if (! $owner) {
            return;
        }

        match ($event->newStatus) {
            'approved' => $owner->notify(new PropertyApprovedNotification($event->property)),
            'rejected' => $owner->notify(new PropertyRejectedNotification($event->property, $event->reason)),
            default => null,
        };
    }
}
