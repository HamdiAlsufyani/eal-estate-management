<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Concerns\ResolvesNotifiableLocale;
use Illuminate\Notifications\Notification;

class UserRegisteredNotification extends Notification
{
    use ResolvesNotifiableLocale;

    public function __construct(private readonly User $user)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $locale = $this->localeFor($notifiable);

        return [
            'type' => 'user_registered',
            'title' => __('notifications.user_registered.title', [], $locale),
            'message' => __('notifications.user_registered.message', [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ], $locale),
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'url' => route('admin.users.show', $this->user),
        ];
    }
}
