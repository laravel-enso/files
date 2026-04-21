<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Requests\Browse as ValidateBrowse;
use LaravelEnso\Files\Http\Resources\Files;

class Favorites extends Controller
{
    public function __invoke(ValidateBrowse $request)
    {
        $files = $request->user()->favoriteFiles()->withData()
            ->between($request->input('interval'))
            ->filter($request->string('query'))
            ->latest('id')
            ->paginate($request->integer('pagination'));

        return new Files($files);
    }
}
