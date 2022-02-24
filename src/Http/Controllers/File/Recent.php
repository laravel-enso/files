<?php

namespace LaravelEnso\Files\Http\Controllers\File;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelEnso\Files\Http\Resources\File as Resource;
use LaravelEnso\Files\Models\File;

class Recent extends Controller
{
    use AuthorizesRequests;

    public function __invoke(Request $request)
    {
        $files = File::for($request->user())
            ->latest('id')
            ->limit(50)
            ->get();

        return Resource::collection($files);
    }
}
