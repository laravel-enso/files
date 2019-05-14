<?php

namespace LaravelEnso\Files;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Files\app\Models\Upload;
use LaravelEnso\Files\app\Services\FileBrowser;
use LaravelEnso\Files\app\Facades\FileBrowser as Browser;

class FileServiceProvider extends ServiceProvider
{
    public $singletons = [
        'file-browser' => FileBrowser::class,
    ];

    public $register = [
        'uploads' => [
            'model' => Upload::class,
            'order' => 100,
        ]
    ];

    public function boot()
    {
        Browser::register($this->register);
    }
}
