<?php

namespace App\Support\Analytics;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DateRange
{
    public const PRESETS = [
        'today', 'yesterday', 'last_7_days', 'last_30_days',
        'this_month', 'last_month', 'this_year', 'custom',
    ];

    public function __construct(
        public readonly string $preset,
        public readonly Carbon $from,
        public readonly Carbon $to,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $preset = $request->string('range')->value() ?: 'last_30_days';

        if (! in_array($preset, self::PRESETS, true)) {
            $preset = 'last_30_days';
        }

        if ($preset === 'custom' && $request->filled('from') && $request->filled('to')) {
            return new self(
                'custom',
                Carbon::parse($request->input('from'))->startOfDay(),
                Carbon::parse($request->input('to'))->endOfDay(),
            );
        }

        [$from, $to] = match ($preset) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'last_7_days' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'this_year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
        };

        return new self($preset === 'custom' ? 'last_30_days' : $preset, $from, $to);
    }
}
