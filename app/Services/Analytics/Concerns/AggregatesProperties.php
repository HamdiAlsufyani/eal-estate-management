<?php

namespace App\Services\Analytics\Concerns;

use App\Models\City;
use App\Models\Favorite;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\PropertyView;
use App\Support\Analytics\DateRange;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Property-scoped analytics aggregation shared by the admin (global) and
 * owner (single-owner) analytics services. Every method accepts an
 * optional `$ownerId` — null means "no ownership restriction" (admin),
 * a user id restricts every query to that owner's properties.
 */
trait AggregatesProperties
{
    protected function statusBreakdown(?int $ownerId): array
    {
        return $this->propertyCountBy('status', ['pending', 'approved', 'rejected'], $ownerId);
    }

    protected function purposeBreakdown(?int $ownerId): array
    {
        return $this->propertyCountBy('purpose', ['sale', 'rent'], $ownerId);
    }

    protected function purposePercentages(?int $ownerId): array
    {
        $counts = $this->purposeBreakdown($ownerId);
        $total = array_sum($counts);

        if ($total === 0) {
            return ['sale' => 0, 'rent' => 0];
        }

        return [
            'sale' => (int) round($counts['sale'] / $total * 100),
            'rent' => (int) round($counts['rent'] / $total * 100),
        ];
    }

    protected function availabilityBreakdown(?int $ownerId): array
    {
        return $this->propertyCountBy('availability', ['available', 'reserved', 'sold', 'rented'], $ownerId);
    }

    private function propertyCountBy(string $column, array $keys, ?int $ownerId): array
    {
        $counts = Property::query()
            ->when($ownerId, fn (Builder $query) => $query->where('user_id', $ownerId))
            ->selectRaw("{$column}, count(*) as aggregate")
            ->groupBy($column)
            ->pluck('aggregate', $column);

        return collect($keys)->mapWithKeys(fn ($key) => [$key => (int) ($counts[$key] ?? 0)])->all();
    }

    protected function topByViews(?int $ownerId, int $limit = 10): Collection
    {
        return $this->topBy('views_count', $ownerId, $limit);
    }

    protected function topByFavorites(?int $ownerId, int $limit = 10): Collection
    {
        return $this->topBy('favorites_count', $ownerId, $limit);
    }

    protected function topByInquiries(?int $ownerId, int $limit = 10): Collection
    {
        return $this->topBy('inquiries_count', $ownerId, $limit);
    }

    private function topBy(string $countColumn, ?int $ownerId, int $limit): Collection
    {
        return Property::query()
            ->when($ownerId, fn (Builder $query) => $query->where('user_id', $ownerId))
            ->withCount(['views', 'favorites', 'inquiries'])
            ->with(['propertyType', 'city', 'user'])
            ->having($countColumn, '>', 0)
            ->orderByDesc($countColumn)
            ->limit($limit)
            ->get();
    }

    /**
     * Top cities ranked by property count, alongside their view/inquiry totals.
     * Three independent grouped queries (no relation joins fanning out rows).
     */
    protected function citiesBreakdown(?int $ownerId, int $limit = 10): Collection
    {
        $propertyCounts = Property::query()
            ->when($ownerId, fn (Builder $query) => $query->where('user_id', $ownerId))
            ->selectRaw('city_id, count(*) as aggregate')
            ->groupBy('city_id')
            ->pluck('aggregate', 'city_id');

        $viewCounts = PropertyView::query()
            ->join('properties', 'properties.id', '=', 'property_views.property_id')
            ->when($ownerId, fn (Builder $query) => $query->where('properties.user_id', $ownerId))
            ->selectRaw('properties.city_id as city_id, count(*) as aggregate')
            ->groupBy('properties.city_id')
            ->pluck('aggregate', 'city_id');

        $inquiryCounts = Inquiry::query()
            ->join('properties', 'properties.id', '=', 'inquiries.property_id')
            ->when($ownerId, fn (Builder $query) => $query->where('properties.user_id', $ownerId))
            ->selectRaw('properties.city_id as city_id, count(*) as aggregate')
            ->groupBy('properties.city_id')
            ->pluck('aggregate', 'city_id');

        $cities = City::query()->whereIn('id', $propertyCounts->keys())->get()->keyBy('id');

        return $propertyCounts
            ->map(fn ($count, $cityId) => [
                'city' => $cities->get($cityId),
                'properties_count' => (int) $count,
                'views_count' => (int) ($viewCounts[$cityId] ?? 0),
                'inquiries_count' => (int) ($inquiryCounts[$cityId] ?? 0),
            ])
            ->filter(fn ($row) => $row['city'] !== null)
            ->sortByDesc('properties_count')
            ->take($limit)
            ->values();
    }

    /**
     * Top property types by number of approved properties.
     */
    protected function typesBreakdown(?int $ownerId, int $limit = 10, bool $approvedOnly = true): Collection
    {
        $counts = Property::query()
            ->when($ownerId, fn (Builder $query) => $query->where('user_id', $ownerId))
            ->when($approvedOnly, fn (Builder $query) => $query->approved())
            ->selectRaw('property_type_id, count(*) as aggregate')
            ->groupBy('property_type_id')
            ->pluck('aggregate', 'property_type_id');

        $types = PropertyType::query()->whereIn('id', $counts->keys())->get()->keyBy('id');

        return $counts
            ->map(fn ($count, $typeId) => [
                'type' => $types->get($typeId),
                'properties_count' => (int) $count,
            ])
            ->filter(fn ($row) => $row['type'] !== null)
            ->sortByDesc('properties_count')
            ->take($limit)
            ->values();
    }

    protected function favoritesByType(?int $ownerId, int $limit = 10): Collection
    {
        return $this->favoritesGroupedBy('property_type_id', PropertyType::class, $ownerId, $limit);
    }

    protected function favoritesByCity(?int $ownerId, int $limit = 10): Collection
    {
        return $this->favoritesGroupedBy('city_id', City::class, $ownerId, $limit);
    }

    private function favoritesGroupedBy(string $column, string $modelClass, ?int $ownerId, int $limit): Collection
    {
        $counts = Favorite::query()
            ->join('properties', 'properties.id', '=', 'favorites.property_id')
            ->when($ownerId, fn (Builder $query) => $query->where('properties.user_id', $ownerId))
            ->selectRaw("properties.{$column} as grouping_id, count(*) as aggregate")
            ->groupBy("properties.{$column}")
            ->pluck('aggregate', 'grouping_id');

        $labels = $modelClass::query()->whereIn('id', $counts->keys())->get()->keyBy('id');

        return $counts
            ->map(fn ($count, $id) => [
                'label' => $labels->get($id),
                'favorites_count' => (int) $count,
            ])
            ->filter(fn ($row) => $row['label'] !== null)
            ->sortByDesc('favorites_count')
            ->take($limit)
            ->values();
    }

    protected function periodPropertiesCount(?int $ownerId, DateRange $range): int
    {
        return Property::query()
            ->when($ownerId, fn (Builder $query) => $query->where('user_id', $ownerId))
            ->whereBetween('created_at', [$range->from, $range->to])
            ->count();
    }

    protected function periodViewsCount(?int $ownerId, DateRange $range): int
    {
        return PropertyView::query()
            ->when($ownerId, fn (Builder $query) => $query->whereHas(
                'property',
                fn (Builder $q) => $q->where('user_id', $ownerId)
            ))
            ->whereBetween('created_at', [$range->from, $range->to])
            ->count();
    }

    protected function periodFavoritesCount(?int $ownerId, DateRange $range): int
    {
        return Favorite::query()
            ->when($ownerId, fn (Builder $query) => $query->whereHas(
                'property',
                fn (Builder $q) => $q->where('user_id', $ownerId)
            ))
            ->whereBetween('created_at', [$range->from, $range->to])
            ->count();
    }

    protected function periodInquiriesCount(?int $ownerId, DateRange $range): int
    {
        return Inquiry::query()
            ->when($ownerId, fn (Builder $query) => $query->whereHas(
                'property',
                fn (Builder $q) => $q->where('user_id', $ownerId)
            ))
            ->whereBetween('created_at', [$range->from, $range->to])
            ->count();
    }

    protected function viewsOverTime(?int $ownerId, DateRange $range, string $groupBy = 'day'): array
    {
        $query = PropertyView::query()
            ->when($ownerId, fn (Builder $q) => $q->whereHas('property', fn (Builder $p) => $p->where('user_id', $ownerId)));

        return $this->series($query, 'created_at', $range, $groupBy);
    }

    protected function favoritesOverTime(?int $ownerId, DateRange $range, string $groupBy = 'day'): array
    {
        $query = Favorite::query()
            ->when($ownerId, fn (Builder $q) => $q->whereHas('property', fn (Builder $p) => $p->where('user_id', $ownerId)));

        return $this->series($query, 'created_at', $range, $groupBy);
    }

    protected function inquiriesOverTime(?int $ownerId, DateRange $range, string $groupBy = 'day'): array
    {
        $query = Inquiry::query()
            ->when($ownerId, fn (Builder $q) => $q->whereHas('property', fn (Builder $p) => $p->where('user_id', $ownerId)));

        return $this->series($query, 'created_at', $range, $groupBy);
    }

    protected function newPropertiesOverTime(?int $ownerId, DateRange $range, string $groupBy = 'day'): array
    {
        $query = Property::query()
            ->when($ownerId, fn (Builder $q) => $q->where('user_id', $ownerId));

        return $this->series($query, 'created_at', $range, $groupBy);
    }

    /**
     * Build a gap-filled label/data series bucketed by day, week, or month.
     * Uses DATE(column), which is portable across MySQL and SQLite (tests),
     * then folds the daily buckets in PHP to avoid non-portable SQL date
     * truncation functions.
     *
     * @return array{labels: array<int, string>, data: array<int, int>}
     */
    protected function series(Builder $query, string $dateColumn, DateRange $range, string $groupBy): array
    {
        $daily = (clone $query)
            ->whereBetween($dateColumn, [$range->from, $range->to])
            ->selectRaw('DATE('.$dateColumn.') as bucket, count(*) as aggregate')
            ->groupBy('bucket')
            ->pluck('aggregate', 'bucket');

        return match ($groupBy) {
            'week' => $this->foldSeries($daily, $range, fn (Carbon $date) => $date->copy()->startOfWeek(), 'M j'),
            'month' => $this->foldSeries($daily, $range, fn (Carbon $date) => $date->copy()->startOfMonth(), 'M Y'),
            default => $this->dailySeries($daily, $range),
        };
    }

    private function dailySeries(Collection $daily, DateRange $range): array
    {
        $labels = [];
        $data = [];

        $cursor = $range->from->copy()->startOfDay();
        $end = $range->to->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $labels[] = $cursor->format('M j');
            $data[] = (int) ($daily[$cursor->format('Y-m-d')] ?? 0);
            $cursor->addDay();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function foldSeries(Collection $daily, DateRange $range, \Closure $bucketStart, string $labelFormat): array
    {
        $buckets = [];

        $cursor = $range->from->copy()->startOfDay();
        $end = $range->to->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $key = $bucketStart($cursor)->format('Y-m-d');
            $buckets[$key] = ($buckets[$key] ?? 0) + (int) ($daily[$cursor->format('Y-m-d')] ?? 0);
            $cursor->addDay();
        }

        ksort($buckets);

        return [
            'labels' => array_map(fn ($key) => Carbon::parse($key)->format($labelFormat), array_keys($buckets)),
            'data' => array_values($buckets),
        ];
    }
}
