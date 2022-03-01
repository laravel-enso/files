<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Resources\File;
use LaravelEnso\Files\Models\Type;

class Browse extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request, Type $type)
    {
        $files = $type->files()
            ->for($request->user())
            ->between(json_decode($request->get('interval'), true))
            ->filter($request->get('query'))
            ->get();

        return File::collection($files);
    }
}
