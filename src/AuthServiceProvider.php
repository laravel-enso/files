<?php

namespace LaravelEnso\Files;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use LaravelEnso\Files\app\Models\File;
use LaravelEnso\Files\app\Models\Upload;
use LaravelEnso\Files\app\Policies\FilePolicy;
use LaravelEnso\Files\app\Policies\UploadPolicy;

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
