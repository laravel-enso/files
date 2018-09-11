<?php

namespace LaravelEnso\FileManager;

use LaravelEnso\FileManager\app\Models\File;
use LaravelEnso\FileManager\app\Policies\FilePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies;

    public function boot()
    {
        $this->policies = [
            File::class => FilePolicy::class,
        ];

        $this->registerPolicies();
    }
}
