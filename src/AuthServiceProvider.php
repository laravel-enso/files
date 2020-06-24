<?php

namespace LaravelEnso\Files;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Files\Models\Upload;
use LaravelEnso\Files\Policies\File as FilePolicy;
use LaravelEnso\Files\Policies\Upload as UploadPolicy;

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
