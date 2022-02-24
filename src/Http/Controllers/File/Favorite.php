<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\Models\Favorite as Model;
use LaravelEnso\Files\Models\File;

class Favorite extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request, File $file)
    {
        $this->authorize('access', $file);

        return ['isFavorite' => Model::toggle($request->user(), $file)];
    }
}
