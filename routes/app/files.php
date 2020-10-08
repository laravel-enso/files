<?php

use Illuminate\Support\Facades\Route;
use LaravelEnso\Files\Http\Controllers\File\Destroy;
use LaravelEnso\Files\Http\Controllers\File\Download;
use LaravelEnso\Files\Http\Controllers\File\Index;
use LaravelEnso\Files\Http\Controllers\File\Link;
use LaravelEnso\Files\Http\Controllers\File\Show;

Route::prefix('files')
    ->as('files.')
    ->group(function () {
        Route::get('', Index::class)->name('index');
        Route::get('link/{file}', Link::class)->name('link');
        Route::get('download/{file}', Download::class)->name('download');
        Route::delete('{file}', Destroy::class)->name('destroy');
        Route::get('show/{file}', Show::class)->name('show');
    });
