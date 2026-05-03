<?php

namespace App\Policies;

use App\Models\Space;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SpacePolicy
{
    public function view(User $user, Space $space): bool
    {
        return $user->id === $space->user_id;
    }

    public function update(User $user, Space $space): bool
    {
        return $user->id === $space->user_id;
    }

    public function delete(User $user, Space $space): bool
    {
        return $user->id === $space->user_id;
    }
}
