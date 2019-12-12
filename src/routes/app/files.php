<?php

Route::prefix('files')->as('files.')
    ->namespace('File')
    ->group(function () {
        Route::get('', 'Index')->name('index');
        Route::get('link/{file}', 'Link')->name('link');
        Route::get('download/{file}', 'Download')->name('download');
        Route::delete('{file}', 'Destroy')->name('destroy');
        Route::get('show/{file}', 'Show')->name('show');
    });
