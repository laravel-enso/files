<?php

Route::middleware(['web', 'auth', 'core'])
    ->namespace('LaravelEnso\Files\app\Http\Controllers')
    ->prefix('api/core')->as('core.')
    ->group(function () {
        Route::prefix('files')->as('files.')
            ->namespace('File')
            ->group(function () {
                Route::get('', 'Index')->name('index');
                Route::get('link/{file}', 'Link')->name('link');
                Route::get('download/{file}', 'Download')->name('download');
                Route::delete('{file}', 'Destroy')->name('destroy');
                Route::get('show/{file}', 'Show')->name('show');
            });

        Route::prefix('uploads')->as('uploads.')
            ->namespace('Upload')
            ->group(function () {
                Route::post('store', 'Store')->name('store');
                Route::delete('{upload}', 'Destroy')->name('destroy');
            });
    });

Route::middleware(['signed', 'bindings'])
    ->prefix('api/core/files')->as('core.files.')
    ->namespace('LaravelEnso\Files\app\Http\Controllers\File')
    ->group(function () {
        Route::get('share/{file}', 'Share')->name('share');
    });
