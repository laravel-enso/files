<?php

use Illuminate\Support\Facades\Route;
use LaravelEnso\Files\Http\Controllers\Upload\Store;
use LaravelEnso\Files\Http\Controllers\Upload\Destroy;

Route::prefix('uploads')
    ->as('uploads.')
    ->group(function () {
        Route::post('store', Store::class)->name('store');
        Route::delete('{upload}', Destroy::class)->name('destroy');
    });
