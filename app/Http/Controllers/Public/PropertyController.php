<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Property;
use App\Models\PropertyType;
use App\Services\PropertySearchService;
use App\Services\PropertyViewService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct(
        private readonly PropertySearchService $search,
        private readonly PropertyViewService $propertyViews,
    ) {
    }

    public function index(Request $request): View
    {
        $filters = $request->only([
            'search', 'purpose', 'property_type', 'city', 'district',
            'price_from', 'price_to', 'area_from', 'area_to',
            'bedrooms', 'bathrooms', 'furnished', 'amenities', 'sort',
        ]);

        return view('public.properties.index', [
            'properties' => $this->search->paginate($filters),
            'filters' => $filters,
            'propertyTypes' => PropertyType::active()->orderBy('name_en')->get(),
            'cities' => City::active()->orderBy('name_en')->get(),
            'districts' => ! empty($filters['city'])
                ? City::find($filters['city'])?->districts()->orderBy('name_en')->get() ?? collect()
                : collect(),
            'amenities' => Amenity::active()->orderBy('name_en')->get(),
        ]);
    }

    public function show(Request $request, Property $property): View
    {
        abort_unless($property->status === 'approved', 404);

        $property->load(['user', 'propertyType', 'city', 'district', 'amenities', 'media']);
        $property->loadCount('views');

        $this->propertyViews->recordView($property, $request->user(), $request->ip(), $request->userAgent());

        $isFavorited = $request->user()
            ? $property->favorites()->where('user_id', $request->user()->id)->exists()
            : false;

        return view('public.properties.show', [
            'property' => $property,
            'isFavorited' => $isFavorited,
        ]);
    }
}
