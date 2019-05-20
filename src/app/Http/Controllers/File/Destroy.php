<?php

namespace LaravelEnso\Files\app\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\app\Models\File;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Destroy extends Controller
{
    use AuthorizesRequests;

    public function __invoke(File $file)
    {
        $this->authorize('handle', $file);

        $file->attachable->delete();
    }
}
