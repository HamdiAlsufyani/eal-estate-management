<?php

namespace App\Notifications\Concerns;

use App\Models\Property;

trait ResolvesNotifiableLocale
{
    /**
     * The recipient's saved locale, not the current request's locale, since
     * notifications may be created outside the original request lifecycle.
     */
    protected function localeFor(object $notifiable): string
    {
        $available = config('app.available_locales', ['en']);
        $locale = $notifiable->locale ?? null;

        return in_array($locale, $available, true) ? $locale : config('app.fallback_locale', 'en');
    }

    protected function propertyTitleFor(Property $property, string $locale): string
    {
        $field = "title_{$locale}";

        return $property->{$field} ?: $property->title_en;
    }
}
