<?php

namespace LaravelEnso\Files\app\Contracts;

use LaravelEnso\Core\app\Models\User;

interface AuthorizesFileAccess
{
    public function viewableBy(User $user): bool;

    public function shareableBy(User $user): bool;

    public function destroyableBy(User $user): bool;
}
