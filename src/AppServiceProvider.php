<?php

namespace LaravelEnso\Files;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\DynamicMethods\Services\Methods;
use LaravelEnso\Files\Dynamics\Relations\FavoriteFiles;
use LaravelEnso\Users\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->load()
            ->publish();

        Methods::bind(User::class, [FavoriteFiles::class]);
    }

    private function load()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->mergeConfigFrom(__DIR__.'/../config/files.php', 'enso.files');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        return $this;
    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/../config' => config_path('enso'),
        ], ['files-config', 'enso-config']);

        $this->publishes([
            __DIR__.'/../database/factories' => database_path('factories'),
        ], ['files-factory', 'enso-factories']);

        return $this;
    }
}
