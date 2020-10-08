<?php

use Illuminate\Support\Facades\Route;
use LaravelEnso\Files\Http\Controllers\File\Share;

Route::middleware(['api', 'auth', 'core'])
    ->prefix('api/core')
    ->as('core.')
    ->group(function () {
        require 'app/files.php';
        require 'app/uploads.php';
    });

Route::middleware(['signed', 'bindings'])
    ->prefix('api/core/files')
    ->as('core.files.')
    ->group(function () {
        Route::get('share/{file}', Share::class)->name('share');
    });
