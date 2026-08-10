<?php

namespace App\Policies;

use App\Models\PropertyType;
use App\Models\User;

class PropertyTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('property_types.manage');
    }

    public function view(User $user, PropertyType $propertyType): bool
    {
        return $user->can('property_types.manage');
    }

    public function create(User $user): bool
    {
        return $user->can('property_types.manage');
    }

    public function update(User $user, PropertyType $propertyType): bool
    {
        return $user->can('property_types.manage');
    }

    public function delete(User $user, PropertyType $propertyType): bool
    {
        return $user->can('property_types.manage');
    }
}
