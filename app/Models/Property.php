<?php

namespace App\Models;

use App\Policies\PropertyPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[UsePolicy(PropertyPolicy::class)]
class Property extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'area' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'furnished' => 'boolean',
            'featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected function title(): Attribute
    {
        return Attribute::get(
            fn () => $this->attributes['title_' . app()->getLocale()] ?: $this->attributes['title_en']
        );
    }

    protected function description(): Attribute
    {
        return Attribute::get(
            fn () => $this->attributes['description_' . app()->getLocale()] ?: $this->attributes['description_en']
        );
    }

    protected function address(): Attribute
    {
        return Attribute::get(
            fn () => $this->attributes['address_' . app()->getLocale()] ?: $this->attributes['address_en']
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('property-images')->useDisk('public');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function views()
    {
        return $this->hasMany(PropertyView::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'property_amenity');
    }

    public function statusHistories()
    {
        return $this->hasMany(PropertyStatusHistory::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability', 'available');
    }

    public function scopeForSale($query)
    {
        return $query->where('purpose', 'sale');
    }

    public function scopeForRent($query)
    {
        return $query->where('purpose', 'rent');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeVisibleTo($query, User $user)
    {
        return $user->canManageAllProperties() ? $query : $query->where('user_id', $user->id);
    }

    public function scopeSearch($query, ?string $term)
    {
        return $query->when($term, fn (Builder $query) => $query->where(
            fn (Builder $query) => $query
                ->where('title_en', 'like', "%{$term}%")
                ->orWhere('title_ar', 'like', "%{$term}%")
                ->orWhere('description_en', 'like', "%{$term}%")
                ->orWhere('description_ar', 'like', "%{$term}%")
                ->orWhere('address_en', 'like', "%{$term}%")
                ->orWhere('address_ar', 'like', "%{$term}%")
                ->orWhereHas('user', fn (Builder $query) => $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"))
                ->orWhereHas('propertyType', fn (Builder $query) => $query
                    ->where('name_en', 'like', "%{$term}%")
                    ->orWhere('name_ar', 'like', "%{$term}%"))
        ));
    }

    public function scopeStatus($query, ?string $status)
    {
        return $query->when($status, fn (Builder $query) => $query->where('status', $status));
    }

    public function scopePurpose($query, ?string $purpose)
    {
        return $query->when($purpose, fn (Builder $query) => $query->where('purpose', $purpose));
    }

    public function scopeAvailabilityIs($query, ?string $availability)
    {
        return $query->when($availability, fn (Builder $query) => $query->where('availability', $availability));
    }

    public function scopeOfType($query, $propertyTypeId)
    {
        return $query->when($propertyTypeId, fn (Builder $query) => $query->where('property_type_id', $propertyTypeId));
    }

    public function scopeInCity($query, $cityId)
    {
        return $query->when($cityId, fn (Builder $query) => $query->where('city_id', $cityId));
    }

    public function scopeInDistrict($query, $districtId)
    {
        return $query->when($districtId, fn (Builder $query) => $query->where('district_id', $districtId));
    }

    public function scopePriceFrom($query, $price)
    {
        return $query->when($price !== null && $price !== '', fn (Builder $query) => $query->where('price', '>=', $price));
    }

    public function scopePriceTo($query, $price)
    {
        return $query->when($price !== null && $price !== '', fn (Builder $query) => $query->where('price', '<=', $price));
    }

    public function scopeAreaFrom($query, $area)
    {
        return $query->when($area !== null && $area !== '', fn (Builder $query) => $query->where('area', '>=', $area));
    }

    public function scopeAreaTo($query, $area)
    {
        return $query->when($area !== null && $area !== '', fn (Builder $query) => $query->where('area', '<=', $area));
    }

    public function scopeMinBedrooms($query, $bedrooms)
    {
        return $query->when($bedrooms !== null && $bedrooms !== '', fn (Builder $query) => $query->where('bedrooms', '>=', $bedrooms));
    }

    public function scopeMinBathrooms($query, $bathrooms)
    {
        return $query->when($bathrooms !== null && $bathrooms !== '', fn (Builder $query) => $query->where('bathrooms', '>=', $bathrooms));
    }

    public function scopeFurnishedIs($query, $furnished)
    {
        return $query->when($furnished !== null && $furnished !== '', fn (Builder $query) => $query->where('furnished', (bool) $furnished));
    }

    public function scopeHasAmenities($query, ?array $amenityIds)
    {
        return $query->when(! empty($amenityIds), function (Builder $query) use ($amenityIds) {
            foreach ($amenityIds as $amenityId) {
                $query->whereHas('amenities', fn (Builder $q) => $q->where('amenities.id', $amenityId));
            }
        });
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->when($userId, fn (Builder $query) => $query->where('user_id', $userId));
    }

    public function scopeFeaturedFilter($query, ?string $value)
    {
        return $query
            ->when($value === 'featured', fn (Builder $query) => $query->where('featured', true))
            ->when($value === 'not_featured', fn (Builder $query) => $query->where('featured', false));
    }

    public function scopeSort($query, ?string $sort)
    {
        return match ($sort) {
            'oldest' => $query->oldest(),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'most_viewed' => $query->withCount('views')->orderByDesc('views_count'),
            'title_asc' => $query->orderBy('title_en'),
            'title_desc' => $query->orderByDesc('title_en'),
            default => $query->latest(),
        };
    }
}
