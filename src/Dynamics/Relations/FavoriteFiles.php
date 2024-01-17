<?php

namespace LaravelEnso\Files\Dynamics\Relations;

use Closure;
use LaravelEnso\DynamicMethods\Contracts\Relation;
use LaravelEnso\Files\Models\Favorite;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Users\Models\User;

class FavoriteFiles implements Relation
{
    public function bindTo(): array
    {
        return [User::class];
    }

    public function name(): string
    {
        return 'favoriteFiles';
    }

    public function closure(): Closure
    {
        return fn (User $user) => $user->hasManyThrough(
            File::class,
            Favorite::class,
            'user_id',
            'id',
            'id',
            'file_id'
        );
    }
}
