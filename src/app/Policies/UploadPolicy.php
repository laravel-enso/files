<?php

namespace LaravelEnso\Files\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use LaravelEnso\Core\app\Models\User;
use LaravelEnso\Files\app\Models\Upload;

class UploadPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin() || $user->isSupervisor()) {
            return true;
        }
    }

    public function view(User $user, Upload $upload)
    {
        return $this->ownsUpload($user, $upload);
    }

    public function share(User $user, Upload $upload)
    {
        return $this->ownsUpload($user, $upload);
    }

    public function destroy(User $user, Upload $upload)
    {
        return $this->ownsUpload($user, $upload);
    }

    private function ownsUpload(User $user, Upload $upload)
    {
        return $user->id === (int) $upload->created_by;
    }
}
