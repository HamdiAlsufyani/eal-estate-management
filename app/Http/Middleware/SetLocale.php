<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = config('app.available_locales', ['en']);

        $locale = session('locale');

        if (! $locale && $request->user()?->locale) {
            $locale = $request->user()->locale;
        }

        if (! in_array($locale, $available, true)) {
            $locale = config('app.locale');
        }

        session(['locale' => $locale]);

        App::setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
