<?php

namespace LaravelEnso\Files\app\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Files\app\Contracts\Attachable;
use LaravelEnso\Files\app\Contracts\AuthorizesFileAccess;
use LaravelEnso\Files\app\Services\UploadManager;
use LaravelEnso\Files\app\Traits\FilePolicies;
use LaravelEnso\Files\app\Traits\HasFile;

class Upload extends Model implements Attachable, AuthorizesFileAccess
{
    use HasFile, FilePolicies;

    protected $optimizeImages = true;

    public static function store(array $files)
    {
        return (new UploadManager($files))
            ->handle();
    }
}
