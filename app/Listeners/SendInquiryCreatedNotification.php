<?php

namespace App\Listeners;

use App\Events\InquiryCreated;
use App\Notifications\InquiryCreatedNotification;

class SendInquiryCreatedNotification
{
    public function handle(InquiryCreated $event): void
    {
        $owner = $event->inquiry->property?->user;

        if (! $owner) {
            return;
        }

        $owner->notify(new InquiryCreatedNotification($event->inquiry));
    }
}
