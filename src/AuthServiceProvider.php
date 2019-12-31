<?php

namespace LaravelEnso\Files;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use LaravelEnso\Files\App\Models\File;
use LaravelEnso\Files\App\Models\Upload;
use LaravelEnso\Files\App\Policies\File as FilePolicy;
use LaravelEnso\Files\App\Policies\Upload as UploadPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Upload::class => UploadPolicy::class,
        File::class => FilePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
