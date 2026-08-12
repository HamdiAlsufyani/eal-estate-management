<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class NotificationsDropdown extends Component
{
    public function render(): View
    {
        $user = auth()->user();

        return view('components.notifications.dropdown', [
            'notifications' => $user ? $user->notifications()->latest()->limit(5)->get() : collect(),
            'unreadCount' => $user ? $user->unreadNotifications()->count() : 0,
        ]);
    }
}
