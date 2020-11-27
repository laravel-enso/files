<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Models\File;

class Share extends Controller
{
    public function __invoke(File $file)
    {
        return $file->download();
    }
}
