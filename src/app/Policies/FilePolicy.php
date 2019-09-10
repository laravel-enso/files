<?php

namespace LaravelEnso\Files\app\Policies;

use LaravelEnso\Core\app\Models\User;
use LaravelEnso\Files\app\Models\File;
use Illuminate\Auth\Access\HandlesAuthorization;
use LaravelEnso\Files\app\Contracts\AuthorizesFileAcces;

class FilePolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin() || $user->isSupervisor()) {
            return true;
        }
    }

    public function view(User $user, File $file)
    {
        return $file->attachable instanceof AuthorizesFileAcces
            ? $this->attachable->viewableBy($user)
            : $this->ownsFile($user, $file);
    }
    
    public function share(User $user, File $file)
    {
        return $file->attachable instanceof AuthorizesFileAcces
            ? $this->attachable->shareableBy($user)
            : $this->ownsFile($user, $file);
    }

    public function destroy(User $user, File $file)
    {
        return $file->attachable instanceof AuthorizesFileAcces
            ? $this->attachable->destroyableBy($user)
            : $this->ownsFile($user, $file);
    }

    private function ownsFile(User $user, File $file)
    {
        return $user->id === (int) $file->created_by;
    }
}
