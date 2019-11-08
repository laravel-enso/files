<?php

namespace LaravelEnso\Files\app\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\app\Models\File;

class Destroy extends Controller
{
    use AuthorizesRequests;

    public function __invoke(File $file)
    {
        $this->authorize('destroy', $file);

        $file->attachable->delete();
    }
}
