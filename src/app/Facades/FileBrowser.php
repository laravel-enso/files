<?php

namespace LaravelEnso\Files\app\Facades;

use Illuminate\Support\Facades\Facade;

class FileBrowser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'file-browser';
    }
}
