<?php

Route::prefix('uploads')->as('uploads.')
    ->namespace('Upload')
    ->group(function () {
        Route::post('store', 'Store')->name('store');
        Route::delete('{upload}', 'Destroy')->name('destroy');
    });
