<?php

namespace App\Listeners;

use App\Events\InquiryStatusChanged;
use App\Notifications\InquiryStatusUpdatedNotification;

class SendInquiryStatusChangedNotification
{
    public function handle(InquiryStatusChanged $event): void
    {
        $customer = $event->inquiry->user;

        if (! $customer) {
            return;
        }

        $customer->notify(new InquiryStatusUpdatedNotification($event->inquiry, $event->newStatus));
    }
}
