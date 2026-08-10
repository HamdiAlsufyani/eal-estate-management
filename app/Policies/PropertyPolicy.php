<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('properties.view');
    }

    public function view(User $user, Property $property): bool
    {
        if (! $user->can('properties.view')) {
            return false;
        }

        return $user->canManageAllProperties() || $user->id === $property->user_id;
    }

    public function create(User $user): bool
    {
        return $user->can('properties.create');
    }

    public function update(User $user, Property $property): bool
    {
        if (! $user->can('properties.edit')) {
            return false;
        }

        if ($user->canManageAllProperties()) {
            return true;
        }

        return $user->id === $property->user_id && in_array($property->status, ['pending', 'rejected'], true);
    }

    public function delete(User $user, Property $property): bool
    {
        if (! $user->can('properties.delete')) {
            return false;
        }

        if ($user->canManageAllProperties()) {
            return true;
        }

        return $user->id === $property->user_id && in_array($property->status, ['pending', 'rejected'], true);
    }

    public function changeStatus(User $user, Property $property): bool
    {
        return $user->can('properties.approve');
    }

    public function changeAvailability(User $user, Property $property): bool
    {
        return $user->can('properties.change_availability');
    }

    public function feature(User $user, Property $property): bool
    {
        return $user->can('properties.feature');
    }

    public function assignOwner(User $user): bool
    {
        return $user->can('properties.assign_owner');
    }
}
