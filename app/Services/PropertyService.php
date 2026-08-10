<?php

namespace App\Services;

use App\Models\Amenity;
use App\Models\City;
use App\Models\District;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PropertyService
{
    private const SALE_AVAILABILITY = ['available', 'reserved', 'sold'];

    private const RENT_AVAILABILITY = ['available', 'reserved', 'rented'];

    public function paginate(array $filters, User $actor): LengthAwarePaginator
    {
        return Property::query()
            ->with(['user', 'propertyType', 'city', 'district'])
            ->withCount('views')
            ->visibleTo($actor)
            ->search($filters['search'] ?? null)
            ->purpose($filters['purpose'] ?? null)
            ->status($filters['status'] ?? null)
            ->availabilityIs($filters['availability'] ?? null)
            ->ofType($filters['property_type'] ?? null)
            ->inCity($filters['city'] ?? null)
            ->inDistrict($filters['district'] ?? null)
            ->ownedBy($filters['owner'] ?? null)
            ->featuredFilter($filters['featured'] ?? null)
            ->sort($filters['sort'] ?? null)
            ->paginate(15)
            ->withQueryString();
    }

    public function create(array $data, User $actor): Property
    {
        return DB::transaction(function () use ($data, $actor) {
            $amenityIds = $data['amenities'] ?? [];
            $images = $data['images'] ?? [];
            unset($data['amenities'], $data['images'], $data['availability']);

            $data['user_id'] = $this->resolveOwnerId($data, $actor);
            $data['status'] = $this->resolveInitialStatus($data, $actor);
            $data['featured'] = $this->resolveFeatured($data, $actor, $data['status']);
            $data['rent_period'] = $data['purpose'] === 'rent' ? ($data['rent_period'] ?? null) : null;
            $data['slug'] = $this->uniqueSlug($data['title']);

            $property = Property::create($data);

            $property->amenities()->sync($amenityIds);

            $property->statusHistories()->create([
                'user_id' => $actor->id,
                'old_status' => null,
                'new_status' => $property->status,
                'reason' => null,
            ]);

            $this->attachImages($property, $images);

            return $property;
        });
    }

    public function update(Property $property, array $data, User $actor): Property
    {
        return DB::transaction(function () use ($property, $data, $actor) {
            $amenityIds = $data['amenities'] ?? null;
            $images = $data['images'] ?? [];
            unset($data['amenities'], $data['images'], $data['status']);

            if ($actor->can('properties.assign_owner') && ! empty($data['user_id'])) {
                // keep the requested owner
            } else {
                unset($data['user_id']);
            }

            if (! $actor->can('properties.feature')) {
                unset($data['featured']);
            } elseif (! empty($data['featured']) && $property->status !== 'approved') {
                throw ValidationException::withMessages([
                    'featured' => 'Only approved properties can be marked as featured.',
                ]);
            }

            if (array_key_exists('availability', $data)) {
                if (! $actor->can('properties.change_availability')) {
                    unset($data['availability']);
                } elseif (! empty($data['availability'])) {
                    $this->assertValidAvailability($property, $data['availability'], $data['purpose'] ?? $property->purpose);
                }
            }

            if (($data['purpose'] ?? $property->purpose) === 'sale') {
                $data['rent_period'] = null;
            }

            if (isset($data['title']) && $data['title'] !== $property->title) {
                $data['slug'] = $this->uniqueSlug($data['title'], $property->id);
            }

            $property->update($data);

            if ($amenityIds !== null) {
                $property->amenities()->sync($amenityIds);
            }

            $this->attachImages($property, $images);

            return $property;
        });
    }

    public function delete(Property $property): bool
    {
        return (bool) $property->delete();
    }

    public function changeAvailability(Property $property, string $newAvailability): Property
    {
        $this->assertValidAvailability($property, $newAvailability, $property->purpose);

        $property->update(['availability' => $newAvailability]);

        return $property;
    }

    public function deleteImage(Property $property, int $mediaId): void
    {
        $media = $property->media()->where('id', $mediaId)->firstOrFail();
        $media->delete();
    }

    public function reorderImages(Property $property, array $mediaIds): void
    {
        $ownedIds = $property->media()->whereIn('id', $mediaIds)->pluck('id')->all();

        \Spatie\MediaLibrary\MediaCollections\Models\Media::setNewOrder(
            array_values(array_intersect($mediaIds, $ownedIds))
        );
    }

    /**
     * @return Collection<int, string>
     */
    public function selectablePropertyTypes(?int $currentId = null): Collection
    {
        return PropertyType::query()
            ->where(fn ($query) => $query->where('is_active', true)->when($currentId, fn ($q) => $q->orWhere('id', $currentId)))
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * @return Collection<int, string>
     */
    public function selectableCities(?int $currentId = null): Collection
    {
        return City::query()
            ->where(fn ($query) => $query->where('is_active', true)->when($currentId, fn ($q) => $q->orWhere('id', $currentId)))
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * @return Collection<int, string>
     */
    public function selectableDistricts(?int $cityId): Collection
    {
        if (! $cityId) {
            return collect();
        }

        return District::query()
            ->where('city_id', $cityId)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    /**
     * Shared dropdown/option data for the property create & edit forms,
     * reused by both the Admin and Owner controllers.
     *
     * @return array<string, mixed>
     */
    public function formData(User $actor, ?Property $property = null): array
    {
        return [
            'propertyTypes' => $this->selectablePropertyTypes($property?->property_type_id),
            'cities' => $this->selectableCities($property?->city_id),
            'districts' => $this->selectableDistricts($property?->city_id),
            'amenities' => Amenity::active()->orderBy('name')->pluck('name', 'id'),
            'owners' => $actor->can('properties.assign_owner')
                ? User::query()->role('Owner')->orderBy('name')->pluck('name', 'id')
                : collect(),
        ];
    }

    private function resolveOwnerId(array $data, User $actor): int
    {
        if ($actor->can('properties.assign_owner') && ! empty($data['user_id'])) {
            return (int) $data['user_id'];
        }

        return $actor->id;
    }

    private function resolveInitialStatus(array $data, User $actor): string
    {
        if ($actor->can('properties.approve') && ! empty($data['status'])) {
            return $data['status'];
        }

        return 'pending';
    }

    private function resolveFeatured(array $data, User $actor, string $status): bool
    {
        $requested = ! empty($data['featured']) && $actor->can('properties.feature');

        if ($requested && $status !== 'approved') {
            throw ValidationException::withMessages([
                'featured' => 'Only approved properties can be marked as featured.',
            ]);
        }

        return $requested;
    }

    private function assertValidAvailability(Property $property, string $availability, string $purpose): void
    {
        if ($property->status !== 'approved') {
            throw ValidationException::withMessages([
                'availability' => 'Only approved properties can have their availability changed.',
            ]);
        }

        $allowed = $purpose === 'sale' ? self::SALE_AVAILABILITY : self::RENT_AVAILABILITY;

        if (! in_array($availability, $allowed, true)) {
            throw ValidationException::withMessages([
                'availability' => "\"{$availability}\" is not a valid availability for a {$purpose} property.",
            ]);
        }
    }

    private function attachImages(Property $property, array $images): void
    {
        foreach ($images as $image) {
            if ($image) {
                $property->addMedia($image)->toMediaCollection('property-images');
            }
        }
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $suffix = 2;

        while (
            Property::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
