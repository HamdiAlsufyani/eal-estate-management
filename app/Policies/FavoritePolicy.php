<?php

namespace App\Policies;

use App\Models\User;

class FavoritePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('favorites.view');
    }
}
