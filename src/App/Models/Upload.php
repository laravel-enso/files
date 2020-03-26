<?php

namespace LaravelEnso\Files\App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Files\App\Contracts\Attachable;
use LaravelEnso\Files\App\Contracts\AuthorizesFileAccess;
use LaravelEnso\Files\App\Services\UploadManager;
use LaravelEnso\Files\App\Traits\FilePolicies;
use LaravelEnso\Files\App\Traits\HasFile;

class Upload extends Model implements Attachable, AuthorizesFileAccess
{
    use HasFile, FilePolicies;

    protected $optimizeImages = true;

    public static function store(array $files)
    {
        return (new UploadManager($files))->handle();
    }
}
