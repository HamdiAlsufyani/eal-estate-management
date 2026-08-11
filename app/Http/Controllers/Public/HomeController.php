<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProperties = Property::query()
            ->approved()
            ->featured()
            ->with(['propertyType', 'city', 'district', 'media', 'favorites' => fn ($query) => $query->where('user_id', auth()->id())])
            ->latest('published_at')
            ->take(6)
            ->get();

        $latestProperties = Property::query()
            ->approved()
            ->with(['propertyType', 'city', 'district', 'media', 'favorites' => fn ($query) => $query->where('user_id', auth()->id())])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->take(8)
            ->get();

        $propertyTypes = PropertyType::query()
            ->active()
            ->withCount(['properties' => fn ($query) => $query->approved()])
            ->orderBy('name_en')
            ->get();

        $cities = City::query()
            ->active()
            ->withCount(['properties' => fn ($query) => $query->approved()])
            ->having('properties_count', '>', 0)
            ->orderByDesc('properties_count')
            ->take(8)
            ->get();

        return view('public.home', [
            'featuredProperties' => $featuredProperties,
            'latestProperties' => $latestProperties,
            'propertyTypes' => $propertyTypes,
            'cities' => $cities,
        ]);
    }
}
