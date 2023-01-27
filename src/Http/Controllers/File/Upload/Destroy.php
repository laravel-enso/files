<?php

namespace LaravelEnso\Files\Http\Controllers\File\Upload;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\Models\Upload;

class Destroy extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Upload $upload)
    {
        $this->authorize('handle', $upload->file);

        $upload->delete();
    }
}
