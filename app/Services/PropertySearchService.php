<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PropertySearchService
{
    private const PER_PAGE = 12;

    private const SORTS = ['oldest', 'price_asc', 'price_desc', 'most_viewed', 'title_asc', 'title_desc'];

    public function paginate(array $filters): LengthAwarePaginator
    {
        $sort = $filters['sort'] ?? null;

        return Property::query()
            ->approved()
            ->with(['propertyType', 'city', 'district', 'media', 'favorites' => fn ($query) => $query->where('user_id', auth()->id())])
            ->withCount('views')
            ->search($filters['search'] ?? null)
            ->purpose($this->purpose($filters['purpose'] ?? null))
            ->ofType($filters['property_type'] ?? null)
            ->inCity($filters['city'] ?? null)
            ->inDistrict($filters['district'] ?? null)
            ->priceFrom($filters['price_from'] ?? null)
            ->priceTo($filters['price_to'] ?? null)
            ->areaFrom($filters['area_from'] ?? null)
            ->areaTo($filters['area_to'] ?? null)
            ->minBedrooms($filters['bedrooms'] ?? null)
            ->minBathrooms($filters['bathrooms'] ?? null)
            ->furnishedIs($filters['furnished'] ?? null)
            ->hasAmenities($this->amenityIds($filters['amenities'] ?? null))
            ->sort(in_array($sort, self::SORTS, true) ? $sort : null)
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    private function purpose(?string $purpose): ?string
    {
        return in_array($purpose, ['sale', 'rent'], true) ? $purpose : null;
    }

    private function amenityIds(mixed $amenities): ?array
    {
        if (empty($amenities)) {
            return null;
        }

        return array_values(array_filter(array_map('intval', (array) $amenities)));
    }
}
