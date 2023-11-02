<?php

namespace LaravelEnso\Files\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use LaravelEnso\Files\Models\File as Model;
use LaravelEnso\Users\Models\User;

class File
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isSuperior()) {
            return true;
        }
    }

    public function access(User $user, Model $file)
    {
        return $file->is_public
            || $this->ownsFile($user, $file)
            || $file->type->isPublic();
    }

    public function manage(User $user, Model $file)
    {
        return $this->ownsFile($user, $file);
    }

    protected function ownsFile(User $user, Model $file)
    {
        return $user->id === (int) $file->created_by;
    }
}
