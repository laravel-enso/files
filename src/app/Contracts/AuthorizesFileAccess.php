<?php

namespace LaravelEnso\Files\App\Contracts;

use LaravelEnso\Core\App\Models\User;

interface AuthorizesFileAccess
{
    public function viewableBy(User $user): bool;

    public function shareableBy(User $user): bool;

    public function destroyableBy(User $user): bool;
}
