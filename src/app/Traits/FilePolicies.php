<?php

namespace LaravelEnso\Files\app\Traits;

use LaravelEnso\Core\app\Models\User;

trait FilePolicies
{
    public function viewableBy(User $user): bool
    {
        return $user->can('view', $this);
    }

    public function shareableBy(User $user): bool
    {
        return $user->can('share', $this);
    }

    public function destroyableBy(User $user): bool
    {
        return $user->can('destroy', $this);
    }
}
