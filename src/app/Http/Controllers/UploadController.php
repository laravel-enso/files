<?php

namespace LaravelEnso\FileManager\app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelEnso\FileManager\app\Models\Upload;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UploadController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Upload $upload)
    {
        return $upload->store($request->allFiles());
    }

    public function destroy(Upload $upload)
    {
        $this->authorize('handle', $upload->file);

        $upload->delete();
    }
}
