<?php

namespace LaravelEnso\Files\app\Policies;

use LaravelEnso\Core\app\Models\User;
use LaravelEnso\Files\app\Models\File;
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
        $attachedTo = $file->attachable->documentable
            ?? $file->attachable;

        if (method_exists($attachedTo, 'canAccess')) {
            return $attachedTo->canAccess($user, $file);
        }

        return $user->id === intval($file->created_by);
    }
}
