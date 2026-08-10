<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;

class CityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('cities.view');
    }

    public function view(User $user, City $city): bool
    {
        return $user->can('cities.view');
    }

    public function create(User $user): bool
    {
        return $user->can('cities.create');
    }

    public function update(User $user, City $city): bool
    {
        return $user->can('cities.edit');
    }

    public function delete(User $user, City $city): bool
    {
        return $user->can('cities.delete');
    }
}
