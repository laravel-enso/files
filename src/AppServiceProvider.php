<?php

namespace LaravelEnso\FileManager;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->mergeConfigFrom(__DIR__.'/config/files.php', 'enso.files');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/resources/js' => resource_path('js'),
        ], 'filemanager-assets');

        $this->publishes([
            __DIR__.'/resources/js' => resource_path('js'),
        ], 'enso-assets');

        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], 'enso-config');

        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], 'filemanager-config');
    }

    public function register()
    {
        //
    }
}
