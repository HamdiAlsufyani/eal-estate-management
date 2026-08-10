<?php

namespace App\Models;

use App\Policies\PropertyPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
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
                ->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%")
                ->orWhereHas('user', fn (Builder $query) => $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"))
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
            'title_asc' => $query->orderBy('title'),
            'title_desc' => $query->orderByDesc('title'),
            default => $query->latest(),
        };
    }
}
