<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Requests\ValidateLink;
use LaravelEnso\Files\Models\File;

class Link extends Controller
{
    use AuthorizesRequests;

    public function __invoke(ValidateLink $request, File $file)
    {
        $this->authorize('access', $file);

        return ['link' => $file->temporaryLink()];
    }
}
