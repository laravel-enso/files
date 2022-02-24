<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Resources\Type as Resource;
use LaravelEnso\Files\Models\Type;

class Index extends Controller
{
    public function __invoke()
    {
        $folders = Type::ordered()->browsable()->get();

        return ['folders' => Resource::collection($folders)];
    }
}
