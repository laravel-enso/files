<?php

Route::middleware(['web', 'auth', 'core'])
    ->namespace('LaravelEnso\Files\app\Http\Controllers')
    ->prefix('api/core')->as('core.')
    ->group(function () {
        require 'app/files.php';
        require 'app/uploads.php';
    });

Route::middleware(['signed', 'bindings'])
    ->prefix('api/core/files')->as('core.files.')
    ->namespace('LaravelEnso\Files\app\Http\Controllers\File')
    ->group(function () {
        Route::get('share/{file}', 'Share')->name('share');
    });
