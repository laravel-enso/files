<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'core'])
    ->namespace('LaravelEnso\Files\App\Http\Controllers')
    ->prefix('api/core')
    ->as('core.')
    ->group(function () {
        require 'app/files.php';
        require 'app/uploads.php';
    });

Route::middleware(['signed', 'bindings'])
    ->namespace('LaravelEnso\Files\App\Http\Controllers\File')
    ->prefix('api/core/files')
    ->as('core.files.')
    ->group(function () {
        Route::get('share/{file}', 'Share')->name('share');
    });
