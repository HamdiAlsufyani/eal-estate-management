<?php

namespace App\Models;

use App\Concerns\HasLocalizedName;
use App\Policies\AmenityPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UsePolicy(AmenityPolicy::class)]
class Amenity extends Model
{
    use HasFactory, HasLocalizedName;

    protected $fillable = [
        'name',
        'name_en',
        'name_ar',
        'slug',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_amenity');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStatus($query, ?string $status)
    {
        return $query->when(
            in_array($status, ['active', 'inactive'], true),
            fn (Builder $query) => $query->where('is_active', $status === 'active')
        );
    }
}
