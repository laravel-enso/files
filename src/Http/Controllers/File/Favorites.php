<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Resources\File;

class Favorites extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request)
    {
        $files = $request->user()->favoriteFiles()->withData()->get();

        return File::collection($files);
    }
}
