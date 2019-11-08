<?php

namespace LaravelEnso\Files\app\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\app\Models\File;

class Link extends Controller
{
    use AuthorizesRequests;

    public function __invoke(File $file)
    {
        $this->authorize('share', $file);

        return ['link' => $file->attachable->temporaryLink()];
    }
}
