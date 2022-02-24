<?php

use Illuminate\Support\Facades\Route;
use LaravelEnso\Files\Http\Controllers\File\Browse;
use LaravelEnso\Files\Http\Controllers\File\Destroy;
use LaravelEnso\Files\Http\Controllers\File\Download;
use LaravelEnso\Files\Http\Controllers\File\Favorite;
use LaravelEnso\Files\Http\Controllers\File\Favorites;
use LaravelEnso\Files\Http\Controllers\File\Index;
use LaravelEnso\Files\Http\Controllers\File\Link;
use LaravelEnso\Files\Http\Controllers\File\Recent;
use LaravelEnso\Files\Http\Controllers\File\SharedByYou;
use LaravelEnso\Files\Http\Controllers\File\SharedWithYou;
use LaravelEnso\Files\Http\Controllers\File\Show;

Route::prefix('files')
    ->as('files.')
    ->group(function () {
        Route::get('', Index::class)->name('index');
        Route::get('link/{file}', Link::class)->name('link');
        Route::get('download/{file}', Download::class)->name('download');
        Route::delete('{file}', Destroy::class)->name('destroy');
        Route::get('show/{file}', Show::class)->name('show');
        Route::get('browse/{type}', Browse::class)->name('browse');
        Route::get('recent', Recent::class)->name('recent');
        Route::get('favorites', Favorites::class)->name('favorites');
        Route::get('sharedByYou', SharedByYou::class)->name('sharedByYou');
        Route::get('sharedWithYou', SharedWithYou::class)->name('sharedWithYou');
        Route::patch('favorite/{file}', Favorite::class)->name('favorite');
    });
