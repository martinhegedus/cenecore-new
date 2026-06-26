<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class AdminContentPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function view(User $user, Model $model): bool
    {
        return (bool) $user->is_admin;
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function update(User $user, Model $model): bool
    {
        return (bool) $user->is_admin;
    }

    public function delete(User $user, Model $model): bool
    {
        return (bool) $user->is_admin;
    }

    public function deleteAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function restore(User $user, Model $model): bool
    {
        return (bool) $user->is_admin;
    }

    public function restoreAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return (bool) $user->is_admin;
    }

    public function forceDeleteAny(User $user): bool
    {
        return (bool) $user->is_admin;
    }

    public function reorder(User $user): bool
    {
        return (bool) $user->is_admin;
    }
}
