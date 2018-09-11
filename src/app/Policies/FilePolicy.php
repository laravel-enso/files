<?php

namespace LaravelEnso\FileManager\app\Policies;

use LaravelEnso\Core\app\Models\User;
use LaravelEnso\FileManager\app\Models\File;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilePolicy
{
    use HandlesAuthorization;

    public function before($user)
    {
        if ($user->isAdmin() || $user->isSupervisor()) {
            return true;
        }
    }

    public function handle(User $user, File $file)
    {
        return $user->id === intval($file->created_by);
    }
}
