<?php

namespace App\Models;

use App\Concerns\HasLocalizedName;
use App\Policies\DistrictPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UsePolicy(DistrictPolicy::class)]
class District extends Model
{
    use HasFactory, HasLocalizedName;

    protected $fillable = [
        'city_id',
        'name',
        'name_en',
        'name_ar',
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
}
