<?php

use Illuminate\Support\Facades\Route;
use LaravelEnso\Files\Http\Controllers\File\Browse;
use LaravelEnso\Files\Http\Controllers\File\Destroy;
use LaravelEnso\Files\Http\Controllers\File\Download;
use LaravelEnso\Files\Http\Controllers\File\Favorite;
use LaravelEnso\Files\Http\Controllers\File\Favorites;
use LaravelEnso\Files\Http\Controllers\File\Link;
use LaravelEnso\Files\Http\Controllers\File\MakePrivate;
use LaravelEnso\Files\Http\Controllers\File\MakePublic;
use LaravelEnso\Files\Http\Controllers\File\Recent;
use LaravelEnso\Files\Http\Controllers\File\Show;
use LaravelEnso\Files\Http\Controllers\File\Update;

Route::prefix('files')
    ->as('files.')
    ->group(function () {
        Route::get('link/{file}', Link::class)->name('link');
        Route::get('download/{file}', Download::class)->name('download');
        Route::delete('{file}', Destroy::class)->name('destroy');
        Route::get('show/{file}', Show::class)->name('show');
        Route::get('browse/{type}', Browse::class)->name('browse');
        Route::get('recent', Recent::class)->name('recent');
        Route::get('favorites', Favorites::class)->name('favorites');
        Route::patch('{file}', Update::class)->name('update');
        Route::patch('makePublic/{file}', MakePublic::class)->name('makePublic');
        Route::patch('makePrivate/{file}', MakePrivate::class)->name('makePrivate');
        Route::patch('favorite/{file}', Favorite::class)->name('favorite');
    });
