<?php

namespace LaravelEnso\Files\App\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\App\Models\File;

class Share extends Controller
{
    public function __invoke(File $file)
    {
        return $file->attachable->download();
    }
}
