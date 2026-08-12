<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\AnalyticsFilterRequest;
use App\Services\Analytics\OwnerAnalyticsService;
use App\Support\Analytics\DateRange;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(private readonly OwnerAnalyticsService $analytics)
    {
    }

    public function index(AnalyticsFilterRequest $request): View
    {
        $owner = $request->user();
        $range = DateRange::fromRequest($request);
        $groupBy = $request->string('group_by')->value() ?: 'day';

        return view('owner.analytics.index', [
            'overview' => $this->analytics->overview($owner),
            'period' => $this->analytics->forPeriod($owner, $range),
            'statusBreakdown' => $this->analytics->statusBreakdownFor($owner),
            'availabilityBreakdown' => $this->analytics->availabilityBreakdownFor($owner),
            'topViewed' => $this->analytics->topViewedFor($owner),
            'topFavorited' => $this->analytics->topFavoritedFor($owner),
            'topInquired' => $this->analytics->topInquiredFor($owner),
            'propertyPerformance' => $this->analytics->propertyPerformance($owner),
            'charts' => $this->analytics->charts($owner, $range, $groupBy),
            'range' => $range,
            'filters' => [
                'range' => $range->preset,
                'from' => $request->input('from'),
                'to' => $request->input('to'),
                'group_by' => $groupBy,
            ],
        ]);
    }
}
