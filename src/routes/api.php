<?php

Route::middleware(['web', 'auth', 'core'])
    ->namespace('LaravelEnso\FileManager\app\Http\Controllers')
    ->prefix('api/core')->as('core.')
    ->group(function () {
        Route::prefix('files')->as('files.')
            ->group(function () {
                Route::get('link/{file}', 'FileController@link')
                    ->name('link');
                Route::get('download/{file}', 'FileController@download')
                    ->name('download');
            });

        Route::resource('files', 'FileController', [
            'only' => ['show', 'index', 'destroy'],
        ]);

        Route::resource('uploads', 'UploadController', [
                'only' => ['store', 'destroy'],
            ]);
    });

Route::middleware(['signed', 'bindings'])
    ->prefix('api/core/files')->as('core.files.')
    ->namespace('LaravelEnso\FileManager\app\Http\Controllers')
    ->group(function () {
        Route::get('share/{file}', 'FileController@share')
            ->name('share');
    });
