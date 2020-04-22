<?php

namespace LaravelEnso\Files\App\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\App\Http\Responses\Files;

class Index extends Controller
{
    public function __invoke()
    {
        return new Files();
    }
}
