<?php

namespace LaravelEnso\Files\app\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\app\Models\File;

class Share extends Controller
{
    public function __invoke(File $file)
    {
        return $file->attachable->download();
    }
}