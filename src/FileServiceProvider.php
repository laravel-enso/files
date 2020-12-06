<?php

namespace LaravelEnso\Files;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Files\Facades\FileBrowser;

class FileServiceProvider extends ServiceProvider
{
    public function boot()
    {
        FileBrowser::register($this->folders());
    }

    public function folders(): array
    {
        return [];
    }
}
