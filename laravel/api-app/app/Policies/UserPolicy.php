<?php
namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user)
    {
        return $user->role->name === 'admin';
    }

    public function view(User $user, User $targetUser)
    {
        return $user->id === $targetUser->id || $user->role->name === 'admin';
    }

    public function create(User $user)
    {
        return $user->role->name === 'admin';
    }

    public function update(User $user, User $targetUser)
    {
        return $user->id === $targetUser->id || $user->role->name === 'admin';
    }

    public function delete(User $user, User $targetUser)
    {
        return $user->id === $targetUser->id ||$user->role->name === 'admin';
    }
}
