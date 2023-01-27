<?php

use Illuminate\Support\Facades\Route;
use LaravelEnso\Files\Http\Controllers\File\Upload\Destroy;
use LaravelEnso\Files\Http\Controllers\File\Upload\Store;

Route::prefix('uploads')
    ->as('uploads.')
    ->group(function () {
        Route::post('store', Store::class)->name('store');
        Route::delete('{upload}', Destroy::class)->name('destroy');
    });
