<?php

namespace App\Services\Analytics;

use App\Models\Favorite;
use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PropertyView;
use App\Models\User;
use App\Services\Analytics\Concerns\AggregatesProperties;
use App\Support\Analytics\DateRange;
use Illuminate\Support\Facades\Cache;

class AdminAnalyticsService
{
    use AggregatesProperties;

    /**
     * Lifetime overview figures — cached briefly since they're expensive
     * and don't need to be perfectly real-time.
     *
     * @return array<string, int>
     */
    public function overview(): array
    {
        return Cache::remember('analytics.admin.overview', 60, function () {
            return [
                'total_users' => User::count(),
                'total_owners' => User::role('Owner')->count(),
                'total_staff' => User::role('Staff')->count(),
                'total_admins' => User::role('Admin')->count(),
                'total_customers' => User::doesntHave('roles')->count(),
                'total_properties' => Property::count(),
                'approved_properties' => Property::where('status', 'approved')->count(),
                'pending_properties' => Property::where('status', 'pending')->count(),
                'rejected_properties' => Property::where('status', 'rejected')->count(),
                'total_inquiries' => Inquiry::count(),
                'total_favorites' => Favorite::count(),
                'total_property_views' => PropertyView::count(),
            ];
        });
    }

    /**
     * @return array<string, int>
     */
    public function userStatusBreakdown(): array
    {
        $counts = User::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'pending' => (int) ($counts['pending'] ?? 0),
            'active' => (int) ($counts['active'] ?? 0),
            'rejected' => (int) ($counts['rejected'] ?? 0),
        ];
    }

    /**
     * @return array<string, int>
     */
    public function inquiryStatusBreakdown(): array
    {
        $counts = Inquiry::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'new' => (int) ($counts['new'] ?? 0),
            'read' => (int) ($counts['read'] ?? 0),
            'closed' => (int) ($counts['closed'] ?? 0),
        ];
    }

    public function newUsersOverTime(DateRange $range, string $groupBy = 'day'): array
    {
        return $this->series(User::query(), 'created_at', $range, $groupBy);
    }

    /**
     * @return array<string, mixed>
     */
    public function forPeriod(DateRange $range): array
    {
        return [
            'new_properties' => $this->periodPropertiesCount(null, $range),
            'new_users' => User::query()->whereBetween('created_at', [$range->from, $range->to])->count(),
            'views' => $this->periodViewsCount(null, $range),
            'favorites' => $this->periodFavoritesCount(null, $range),
            'inquiries' => $this->periodInquiriesCount(null, $range),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function charts(DateRange $range, string $groupBy = 'day'): array
    {
        return [
            'views_over_time' => $this->viewsOverTime(null, $range, $groupBy),
            'new_properties_over_time' => $this->newPropertiesOverTime(null, $range, $groupBy),
            'new_users_over_time' => $this->newUsersOverTime($range, $groupBy),
            'inquiries_over_time' => $this->inquiriesOverTime(null, $range, $groupBy),
            'favorites_over_time' => $this->favoritesOverTime(null, $range, $groupBy),
        ];
    }

    public function statusBreakdownGlobal(): array
    {
        return $this->statusBreakdown(null);
    }

    public function purposeBreakdownGlobal(): array
    {
        return $this->purposeBreakdown(null);
    }

    public function purposePercentagesGlobal(): array
    {
        return $this->purposePercentages(null);
    }

    public function availabilityBreakdownGlobal(): array
    {
        return $this->availabilityBreakdown(null);
    }

    public function topViewedGlobal(int $limit = 10)
    {
        return $this->topByViews(null, $limit);
    }

    public function topFavoritedGlobal(int $limit = 10)
    {
        return $this->topByFavorites(null, $limit);
    }

    public function topInquiredGlobal(int $limit = 10)
    {
        return $this->topByInquiries(null, $limit);
    }

    public function citiesGlobal(int $limit = 10)
    {
        return $this->citiesBreakdown(null, $limit);
    }

    public function typesGlobal(int $limit = 10)
    {
        return $this->typesBreakdown(null, $limit);
    }

    public function favoritesByTypeGlobal(int $limit = 10)
    {
        return $this->favoritesByType(null, $limit);
    }

    public function favoritesByCityGlobal(int $limit = 10)
    {
        return $this->favoritesByCity(null, $limit);
    }
}
