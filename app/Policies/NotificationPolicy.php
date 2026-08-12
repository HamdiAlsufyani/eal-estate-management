<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
    public function update(User $user, DatabaseNotification $notification): bool
    {
        return $this->owns($user, $notification);
    }

    public function delete(User $user, DatabaseNotification $notification): bool
    {
        return $this->owns($user, $notification);
    }

    private function owns(User $user, DatabaseNotification $notification): bool
    {
        return $notification->notifiable_type === User::class
            && $notification->notifiable_id === $user->id;
    }
}
