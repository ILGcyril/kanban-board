<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\Space;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BoardPolicy
{
    public function view(User $user, Board $board): bool
    {
        return $board->space()->where('user_id', $user->id)->exists();
    }

    public function create(User $user, Space $space): bool
    {
        return $user->id === $space->user_id;
    }

    public function update(User $user, Board $board): bool
    {
        return $board->space()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, Board $board): bool
    {
        return $board->space()->where('user_id', $user->id)->exists();
    }
}
