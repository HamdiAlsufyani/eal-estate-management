<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function read(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        $this->authorize('update', $notification);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect($notification->data['url'] ?? route('notifications.index'));
    }

    public function readAll(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }

    public function destroy(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        $this->authorize('delete', $notification);

        $notification->delete();

        return back()->with('success', __('notifications.deleted'));
    }
}
