<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\UserRegisteredNotification;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Permission;

class SendUserRegisteredNotification
{
    public function handle(Registered $event): void
    {
        if (! Permission::where('name', 'users.approve')->where('guard_name', 'web')->exists()) {
            return;
        }

        $recipients = User::query()->permission('users.approve')->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new UserRegisteredNotification($event->user));
        }
    }
}
