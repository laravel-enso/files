<?php

namespace LaravelEnso\Files\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use LaravelEnso\Files\Models\Upload as Model;
use LaravelEnso\Users\Models\User;

class Upload
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        return $user->isSuperior();
    }

    public function view(User $user, Model $upload)
    {
        return $this->ownsUpload($user, $upload);
    }

    public function share(User $user, Model $upload)
    {
        return $this->ownsUpload($user, $upload);
    }

    public function destroy(User $user, Model $upload)
    {
        return $this->ownsUpload($user, $upload);
    }

    private function ownsUpload(User $user, Model $upload)
    {
        return $user->id === (int) $upload->created_by;
    }
}
