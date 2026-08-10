<?php

namespace App\Policies;

use App\Models\District;
use App\Models\User;

class DistrictPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('districts.view');
    }

    public function view(User $user, District $district): bool
    {
        return $user->can('districts.view');
    }

    public function create(User $user): bool
    {
        return $user->can('districts.create');
    }

    public function update(User $user, District $district): bool
    {
        return $user->can('districts.edit');
    }

    public function delete(User $user, District $district): bool
    {
        return $user->can('districts.delete');
    }
}
