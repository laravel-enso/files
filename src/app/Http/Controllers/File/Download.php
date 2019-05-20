<?php

namespace LaravelEnso\Files\app\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\app\Models\File;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Download extends Controller
{
    use AuthorizesRequests;

    public function __invoke(File $file)
    {
        $this->authorize('handle', $file);

        return $file->attachable->download();
    }
}
