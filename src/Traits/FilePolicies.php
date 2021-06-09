<?php

namespace LaravelEnso\Files\Traits;

use LaravelEnso\Users\Models\User;

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
