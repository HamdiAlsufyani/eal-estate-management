<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PropertyView;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'total_properties' => Property::count(),
            'pending_properties' => Property::where('status', 'pending')->count(),
            'approved_properties' => Property::where('status', 'approved')->count(),
            'rejected_properties' => Property::where('status', 'rejected')->count(),
            'total_users' => User::count(),
            'pending_users' => User::where('status', 'pending')->count(),
            'property_views' => PropertyView::count(),
        ];

        $latestProperties = Property::with(['city', 'propertyType'])
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::latest()->take(5)->get();

        $latestInquiries = Inquiry::with(['property', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'latestProperties',
            'recentUsers',
            'latestInquiries',
        ));
    }
}
