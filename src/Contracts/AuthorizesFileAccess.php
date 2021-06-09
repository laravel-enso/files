<?php

namespace LaravelEnso\Files\Contracts;

use LaravelEnso\Users\Models\User;

interface AuthorizesFileAccess
{
    public function viewableBy(User $user): bool;

    public function shareableBy(User $user): bool;

    public function destroyableBy(User $user): bool;
}
