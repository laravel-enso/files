<?php

namespace LaravelEnso\Files;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Files\App\Services\FileBrowser;

class AppServiceProvider extends ServiceProvider
{
    public $singletons = [
        'file-browser' => FileBrowser::class,
    ];

    public function boot()
    {
        $this->load()
            ->publish();
    }

    private function load()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');

        $this->mergeConfigFrom(__DIR__.'/config/files.php', 'enso.files');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        return $this;
    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], ['files-config', 'enso-config']);

        $this->publishes([
            __DIR__.'/../stubs/FileServiceProvider.stub' => app_path(
                'Providers/FileServiceProvider.php'
            ),
        ], 'file-provider');
    }
}
