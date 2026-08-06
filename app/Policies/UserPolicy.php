<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('users.edit');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('users.delete') && $user->isNot($model);
    }

    public function approve(User $user, User $model): bool
    {
        return $user->can('users.approve') && $user->isNot($model);
    }

    public function reject(User $user, User $model): bool
    {
        return $user->can('users.approve') && $user->isNot($model);
    }

    public function suspend(User $user, User $model): bool
    {
        return $user->can('users.approve') && $user->isNot($model);
    }

    public function activate(User $user, User $model): bool
    {
        return $user->can('users.approve') && $user->isNot($model);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('users.delete');
    }

    public function bulkAction(User $user): bool
    {
        return $user->can('users.approve');
    }
}
