<?php

namespace LaravelEnso\Files\DynamicsRelations;

use Closure;
use LaravelEnso\DynamicMethods\Contracts\Method;
use LaravelEnso\Files\Models\Upload;

class Uploads implements Method
{
    public function name(): string
    {
        return 'uploads';
    }

    public function closure(): Closure
    {
        return fn () => $this->hasMany(Upload::class, 'created_by');
    }
}
