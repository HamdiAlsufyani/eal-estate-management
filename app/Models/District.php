<?php

namespace App\Models;

use App\Policies\DistrictPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UsePolicy(DistrictPolicy::class)]
class District extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name',
        'slug',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function scopeCity($query, $cityId)
    {
        return $query->when($cityId, fn (Builder $query) => $query->where('city_id', $cityId));
    }

    public function scopeSearch($query, ?string $term)
    {
        return $query->when($term, fn (Builder $query) => $query->where(
            fn (Builder $query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
        ));
    }

    public function scopeSort($query, ?string $sort)
    {
        return match ($sort) {
            'oldest' => $query->oldest(),
            'name_asc' => $query->orderBy('name'),
            'name_desc' => $query->orderByDesc('name'),
            default => $query->latest(),
        };
    }
}
