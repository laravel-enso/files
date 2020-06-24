<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Upload')
    ->prefix('uploads')
    ->as('uploads.')
    ->group(function () {
        Route::post('store', 'Store')->name('store');
        Route::delete('{upload}', 'Destroy')->name('destroy');
    });
