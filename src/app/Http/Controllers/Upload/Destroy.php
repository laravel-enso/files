<?php

namespace LaravelEnso\Files\app\Http\Controllers\Upload;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\app\Models\Upload;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Destroy extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Upload $upload)
    {
        $this->authorize('handle', $upload->file);

        $upload->delete();
    }
}
