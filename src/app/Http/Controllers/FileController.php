<?php

namespace LaravelEnso\FileManager\app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelEnso\FileManager\app\Models\File;
use LaravelEnso\FileManager\app\Http\Resources\Collection;
use LaravelEnso\FileManager\app\Http\Resources\File as Resource;

class FileController extends Controller
{
    public function index(Request $request)
    {
        return new Collection(
            Resource::collection(
                File::visible()
                    ->with(['createdBy', 'attachable'])
                    ->forUser($request->user())
                    ->between(json_decode($request->get('interval')))
                    ->ordered()
                    ->get()
                )
            );
    }

    public function show(File $file)
    {
        $this->authorize('handle', $file);

        return $file->attachable->inline();
    }

    public function download(File $file)
    {
        $this->authorize('handle', $file);

        return $file->attachable->download();
    }

    public function link(File $file)
    {
        $this->authorize('handle', $file);

        return ['link' => $file->temporaryLink()];
    }

    public function share(File $file)
    {
        return $file->attachable->download();
    }

    public function destroy(File $file)
    {
        $this->authorize('handle', $file);

        $file->attachable->delete();
    }
}
