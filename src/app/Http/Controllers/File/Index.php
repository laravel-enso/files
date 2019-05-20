<?php

namespace LaravelEnso\Files\app\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\app\Http\Responses\Files;

class Index extends Controller
{
    public function __invoke()
    {
        return new Files();
    }
}
