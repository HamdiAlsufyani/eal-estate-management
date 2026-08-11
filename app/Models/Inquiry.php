<?php

namespace App\Models;

use App\Policies\InquiryPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[UsePolicy(InquiryPolicy::class)]
class Inquiry extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, ?string $term)
    {
        return $query->when($term, fn (Builder $query) => $query->where(
            fn (Builder $query) => $query
                ->where('phone', 'like', "%{$term}%")
                ->orWhereHas('user', fn (Builder $query) => $query
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"))
                ->orWhereHas('property', fn (Builder $query) => $query
                    ->where('title', 'like', "%{$term}%")
                    ->orWhereHas('user', fn (Builder $query) => $query->where('name', 'like', "%{$term}%")))
        ));
    }

    public function scopeStatus($query, ?string $status)
    {
        return $query->when($status, fn (Builder $query) => $query->where('status', $status));
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->when($propertyId, fn (Builder $query) => $query->where('property_id', $propertyId));
    }

    public function scopeForOwner($query, $ownerId)
    {
        return $query->when($ownerId, fn (Builder $query) => $query->whereHas(
            'property',
            fn (Builder $query) => $query->where('user_id', $ownerId)
        ));
    }

    public function scopeDateFrom($query, $date)
    {
        return $query->when($date, fn (Builder $query) => $query->whereDate('created_at', '>=', $date));
    }

    public function scopeDateTo($query, $date)
    {
        return $query->when($date, fn (Builder $query) => $query->whereDate('created_at', '<=', $date));
    }

    public function scopeSort($query, ?string $sort)
    {
        return match ($sort) {
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };
    }
}
