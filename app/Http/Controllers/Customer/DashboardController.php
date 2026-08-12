<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\PropertyViewService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly PropertyViewService $propertyViews)
    {
    }

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'total_favorites' => $user->favorites()->count(),
            'active_inquiries' => $user->inquiries()->whereIn('status', ['new', 'read'])->count(),
            'completed_inquiries' => $user->inquiries()->where('status', 'closed')->count(),
            'recently_viewed' => $this->propertyViews->countUniqueForUser($user),
            'unread_notifications' => $user->unreadNotifications()->count(),
        ];

        $recentFavorites = $user->favorites()
            ->with(['property.propertyType', 'property.city', 'property.district', 'property.media'])
            ->latest()
            ->take(6)
            ->get();

        $recentInquiries = $user->inquiries()
            ->with(['property', 'property.user'])
            ->latest()
            ->take(5)
            ->get();

        $latestNotifications = $user->notifications()->latest()->take(5)->get();

        $recentlyViewed = $this->propertyViews->latestForUser($user, 6);

        return view('customer.dashboard', compact(
            'stats',
            'recentFavorites',
            'recentInquiries',
            'latestNotifications',
            'recentlyViewed',
        ));
    }
}
