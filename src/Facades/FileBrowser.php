<?php

namespace LaravelEnso\Files\Facades;

use Illuminate\Support\Facades\Facade;

class FileBrowser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'file-browser';
    }
}
