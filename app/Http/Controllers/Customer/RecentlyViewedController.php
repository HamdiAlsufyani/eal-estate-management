<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyView;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class RecentlyViewedController extends Controller
{
    public function __invoke(Request $request): View
    {
        $lastViewedAt = PropertyView::query()
            ->where('user_id', $request->user()->id)
            ->selectRaw('property_id, MAX(created_at) as viewed_at')
            ->groupBy('property_id')
            ->orderByDesc('viewed_at')
            ->limit(20)
            ->pluck('viewed_at', 'property_id')
            ->map(fn (string $viewedAt) => Carbon::parse($viewedAt));

        $properties = Property::withTrashed()
            ->whereIn('id', $lastViewedAt->keys())
            ->with(['propertyType', 'city', 'district', 'media'])
            ->get()
            ->sortByDesc(fn (Property $property) => $lastViewedAt[$property->id])
            ->values();

        return view('customer.recently-viewed', [
            'properties' => $properties,
            'lastViewedAt' => $lastViewedAt,
        ]);
    }
}
