<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Policies\UserPolicy;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'phone', 'password', 'status', 'profile_photo', 'locale'])]
#[Hidden(['password', 'remember_token'])]
#[UsePolicy(UserPolicy::class)]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function canManageAllProperties(): bool
    {
        return $this->can('properties.approve');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function propertyViews()
    {
        return $this->hasMany(PropertyView::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeStatus($query, ?string $status)
    {
        return $query->when($status, fn (Builder $query) => $query->where('status', $status));
    }

    public function scopeRole($query, ?string $role)
    {
        return $query->when($role, fn (Builder $query) => $query->whereHas(
            'roles',
            fn (Builder $query) => $query->where('name', $role)
        ));
    }

    public function scopeSearch($query, ?string $term)
    {
        return $query->when($term, fn (Builder $query) => $query->where(
            fn (Builder $query) => $query
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
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

    public function profilePhotoUrl(): ?string
    {
        return $this->profile_photo
            ? Storage::disk('public')->url($this->profile_photo)
            : null;
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
    }
}
