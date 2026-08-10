<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\PropertyView;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $owner = $request->user();

        $stats = [
            'total_properties' => $owner->properties()->count(),
            'pending_properties' => $owner->properties()->where('status', 'pending')->count(),
            'approved_properties' => $owner->properties()->where('status', 'approved')->count(),
            'rejected_properties' => $owner->properties()->where('status', 'rejected')->count(),
            'available_properties' => $owner->properties()->where('availability', 'available')->count(),
            'sold_properties' => $owner->properties()->where('availability', 'sold')->count(),
            'rented_properties' => $owner->properties()->where('availability', 'rented')->count(),
            'total_views' => PropertyView::whereHas('property', fn ($query) => $query->where('user_id', $owner->id))->count(),
        ];

        $recentProperties = $owner->properties()
            ->with(['propertyType', 'city'])
            ->latest()
            ->take(5)
            ->get();

        return view('owner.dashboard', compact('stats', 'recentProperties'));
    }
}
