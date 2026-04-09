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
        $files = $request->user()->favoriteFiles()->withData()
            ->between($request->get('interval'))
            ->filter($request->get('query'))
            ->paginated()->latest('id')->get();

        return File::collection($files);
    }
}
