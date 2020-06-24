<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Responses\Files;

class Index extends Controller
{
    public function __invoke()
    {
        return new Files();
    }
}
