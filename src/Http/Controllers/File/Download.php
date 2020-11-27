<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\Models\File;

class Download extends Controller
{
    use AuthorizesRequests;

    public function __invoke(File $file)
    {
        $this->authorize('view', $file);

        return $file->download();
    }
}
