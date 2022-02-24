<?php

namespace LaravelEnso\Files\Dynamics\Relations;

use Closure;
use LaravelEnso\DynamicMethods\Contracts\Method;
use LaravelEnso\Files\Models\Favorite;
use LaravelEnso\Files\Models\File;

class FavoriteFiles implements Method
{
    public function name(): string
    {
        return 'favoriteFiles';
    }

    public function closure(): Closure
    {
        return fn () => $this->hasManyThrough(
            File::class,
            Favorite::class,
            'user_id',
            'id',
            'id',
            'file_id'
        );
    }
}
