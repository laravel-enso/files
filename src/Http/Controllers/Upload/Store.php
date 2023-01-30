<?php

namespace LaravelEnso\Files\Http\Controllers\Upload;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Resources\File;
use LaravelEnso\Files\Models\Upload;

class Store extends Controller
{
    public function __invoke(Request $request, Upload $upload)
    {
        $files = $upload->store($request->allFiles());
        $files->each->loadData();

        return File::collection($files);
    }
}
