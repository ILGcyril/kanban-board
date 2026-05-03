<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function create(User $user, Board $board): bool
    {
        return $board->space()->where('user_id', $user->id)->exists();
    }

    public function update(User $user, Task $task): bool
    {
        return $task->board->space()->where('user_id', $user->id)->exists();
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->board->space()->where('user_id', $user->id)->exists();
    }
}
