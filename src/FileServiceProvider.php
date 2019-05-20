<?php

namespace LaravelEnso\Files;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Files\app\Models\Upload;
use LaravelEnso\Files\app\Facades\FileBrowser;

class FileServiceProvider extends ServiceProvider
{
    public $register = [];

    public function boot()
    {
        FileBrowser::register($this->register);
    }
}
