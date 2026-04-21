<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Requests\Browse as ValidateBrowse;
use LaravelEnso\Files\Http\Resources\Files;
use LaravelEnso\Files\Models\File;

class Recent extends Controller
{
    public function __invoke(ValidateBrowse $request)
    {
        $files = File::for($request->user())
            ->between($request->input('interval'))
            ->filter($request->string('query'))
            ->latest('id')
            ->paginate($request->integer('pagination'));

        return new Files($files);
    }
}
