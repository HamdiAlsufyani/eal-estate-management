<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\AnalyticsFilterRequest;
use App\Services\Analytics\AdminAnalyticsService;
use App\Support\Analytics\DateRange;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __construct(private readonly AdminAnalyticsService $analytics)
    {
    }

    public function index(AnalyticsFilterRequest $request): View
    {
        $range = DateRange::fromRequest($request);
        $groupBy = $request->string('group_by')->value() ?: 'day';

        return view('admin.analytics.index', [
            'overview' => $this->analytics->overview(),
            'period' => $this->analytics->forPeriod($range),
            'statusBreakdown' => $this->analytics->statusBreakdownGlobal(),
            'purposeBreakdown' => $this->analytics->purposeBreakdownGlobal(),
            'purposePercentages' => $this->analytics->purposePercentagesGlobal(),
            'availabilityBreakdown' => $this->analytics->availabilityBreakdownGlobal(),
            'userStatusBreakdown' => $this->analytics->userStatusBreakdown(),
            'inquiryStatusBreakdown' => $this->analytics->inquiryStatusBreakdown(),
            'topViewed' => $this->analytics->topViewedGlobal(),
            'topFavorited' => $this->analytics->topFavoritedGlobal(),
            'topInquired' => $this->analytics->topInquiredGlobal(),
            'cities' => $this->analytics->citiesGlobal(),
            'types' => $this->analytics->typesGlobal(),
            'favoritesByType' => $this->analytics->favoritesByTypeGlobal(),
            'favoritesByCity' => $this->analytics->favoritesByCityGlobal(),
            'charts' => $this->analytics->charts($range, $groupBy),
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
