<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
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
                'status',
    'profile_photo',
        ];
    }
       public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }
    public function scopeActive($query)
{
    return $query->where('status', 'active');
}

public function scopePending($query)
{
    return $query->where('status', 'pending');
}
}
