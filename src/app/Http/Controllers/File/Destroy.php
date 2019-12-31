<?php

namespace LaravelEnso\Files\App\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\App\Models\File;

class Destroy extends Controller
{
    use AuthorizesRequests;

    public function __invoke(File $file)
    {
        $this->authorize('destroy', $file);

        $file->attachable->delete();
    }
}
