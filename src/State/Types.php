<?php

namespace LaravelEnso\Files\State;

use LaravelEnso\Core\Contracts\ProvidesState;
use LaravelEnso\Files\Http\Resources\Type as Resource;
use LaravelEnso\Files\Models\Type;

class Types implements ProvidesState
{
    public function store(): string
    {
        return 'files';
    }

    public function state(): array
    {
        return Resource::collection(Type::ordered()->get())->resolve();
    }
}
