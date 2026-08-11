<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasLocalizedName
{
    protected function name(): Attribute
    {
        return Attribute::get(
            fn () => $this->attributes['name_' . app()->getLocale()] ?: $this->attributes['name_en']
        );
    }

    public function scopeSearch($query, ?string $term)
    {
        return $query->when($term, fn ($query) => $query->where(
            fn ($query) => $query
                ->where('name_en', 'like', "%{$term}%")
                ->orWhere('name_ar', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
        ));
    }

    public function scopeSort($query, ?string $sort)
    {
        return match ($sort) {
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name_en'),
            'name_desc' => $query->orderByDesc('name_en'),
            default => $query->latest(),
        };
    }
}
