<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyView;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PropertyViewService
{
    private const DEDUPE_WINDOW_HOURS = 24;

    public function recordView(Property $property, ?User $user, string $ip, ?string $userAgent): void
    {
        if ($property->status !== 'approved') {
            return;
        }

        $existing = PropertyView::query()
            ->where('property_id', $property->id)
            ->where('updated_at', '>=', now()->subHours(self::DEDUPE_WINDOW_HOURS))
            ->when($user, fn ($query) => $query->where('user_id', $user->id))
            ->when(! $user, fn ($query) => $query->whereNull('user_id')->where('ip_address', $ip))
            ->first();

        if ($existing) {
            $existing->touch();

            return;
        }

        PropertyView::create([
            'property_id' => $property->id,
            'user_id' => $user?->id,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);
    }

    public function paginateForUser(User $user, ?string $purpose, int $perPage = 20): LengthAwarePaginator
    {
        $paginator = $this->baseQueryForUser($user, $purpose)->paginate($perPage)->withQueryString();

        $paginator->getCollection()->each($this->castLastViewedAt(...));

        return $paginator;
    }

    public function latestForUser(User $user, int $limit = 6): Collection
    {
        $properties = $this->baseQueryForUser($user, null)->limit($limit)->get();

        $properties->each($this->castLastViewedAt(...));

        return $properties;
    }

    public function countUniqueForUser(User $user): int
    {
        return PropertyView::where('user_id', $user->id)->distinct('property_id')->count('property_id');
    }

    public function remove(User $user, Property $property): void
    {
        $user->propertyViews()->where('property_id', $property->id)->delete();
    }

    public function clear(User $user): void
    {
        $user->propertyViews()->delete();
    }

    private function baseQueryForUser(User $user, ?string $purpose): Builder
    {
        $latestViews = DB::table('property_views')
            ->select('property_id', DB::raw('MAX(updated_at) as last_viewed_at'))
            ->where('user_id', $user->id)
            ->groupBy('property_id');

        return Property::withTrashed()
            ->joinSub($latestViews, 'latest_views', fn ($join) => $join->on('properties.id', '=', 'latest_views.property_id'))
            ->addSelect('properties.*', 'latest_views.last_viewed_at')
            ->when($purpose, fn (Builder $query) => $query->purpose($purpose))
            ->with(['propertyType', 'city', 'district', 'media'])
            ->orderByDesc('latest_views.last_viewed_at');
    }

    private function castLastViewedAt(Property $property): void
    {
        $property->setAttribute('last_viewed_at', Carbon::parse($property->getAttribute('last_viewed_at')));
    }
}
