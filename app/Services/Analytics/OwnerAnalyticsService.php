<?php

namespace App\Services\Analytics;

use App\Models\Property;
use App\Models\User;
use App\Services\Analytics\Concerns\AggregatesProperties;
use App\Support\Analytics\DateRange;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class OwnerAnalyticsService
{
    use AggregatesProperties;

    /**
     * Lifetime overview figures scoped strictly to the given owner.
     *
     * @return array<string, int>
     */
    public function overview(User $owner): array
    {
        return Cache::remember("analytics.owner.{$owner->id}.overview", 60, function () use ($owner) {
            $status = $this->statusBreakdown($owner->id);
            $availability = $this->availabilityBreakdown($owner->id);

            return [
                'total_properties' => array_sum($status),
                'approved_properties' => $status['approved'],
                'pending_properties' => $status['pending'],
                'rejected_properties' => $status['rejected'],
                'available_properties' => $availability['available'],
                'reserved_properties' => $availability['reserved'],
                'sold_properties' => $availability['sold'],
                'rented_properties' => $availability['rented'],
                'total_views' => $this->lifetimeViews($owner->id),
                'total_favorites' => $this->lifetimeFavorites($owner->id),
                'total_inquiries' => $this->lifetimeInquiries($owner->id),
            ];
        });
    }

    private function lifetimeViews(int $ownerId): int
    {
        return \App\Models\PropertyView::query()
            ->whereHas('property', fn ($q) => $q->where('user_id', $ownerId))
            ->count();
    }

    private function lifetimeFavorites(int $ownerId): int
    {
        return \App\Models\Favorite::query()
            ->whereHas('property', fn ($q) => $q->where('user_id', $ownerId))
            ->count();
    }

    private function lifetimeInquiries(int $ownerId): int
    {
        return \App\Models\Inquiry::query()
            ->whereHas('property', fn ($q) => $q->where('user_id', $ownerId))
            ->count();
    }

    /**
     * @return array<string, mixed>
     */
    public function forPeriod(User $owner, DateRange $range): array
    {
        return [
            'new_properties' => $this->periodPropertiesCount($owner->id, $range),
            'views' => $this->periodViewsCount($owner->id, $range),
            'favorites' => $this->periodFavoritesCount($owner->id, $range),
            'inquiries' => $this->periodInquiriesCount($owner->id, $range),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function charts(User $owner, DateRange $range, string $groupBy = 'day'): array
    {
        return [
            'views_over_time' => $this->viewsOverTime($owner->id, $range, $groupBy),
            'favorites_over_time' => $this->favoritesOverTime($owner->id, $range, $groupBy),
            'inquiries_over_time' => $this->inquiriesOverTime($owner->id, $range, $groupBy),
        ];
    }

    public function statusBreakdownFor(User $owner): array
    {
        return $this->statusBreakdown($owner->id);
    }

    public function availabilityBreakdownFor(User $owner): array
    {
        return $this->availabilityBreakdown($owner->id);
    }

    public function topViewedFor(User $owner, int $limit = 10)
    {
        return $this->topByViews($owner->id, $limit);
    }

    public function topFavoritedFor(User $owner, int $limit = 10)
    {
        return $this->topByFavorites($owner->id, $limit);
    }

    public function topInquiredFor(User $owner, int $limit = 10)
    {
        return $this->topByInquiries($owner->id, $limit);
    }

    /**
     * Full property performance table for the owner (paginated — never
     * loads every row into PHP at once).
     */
    public function propertyPerformance(User $owner): LengthAwarePaginator
    {
        return Property::query()
            ->where('user_id', $owner->id)
            ->withCount(['views', 'favorites', 'inquiries'])
            ->with(['propertyType', 'city'])
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }
}
