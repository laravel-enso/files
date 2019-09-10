<?php

namespace LaravelEnso\Files\app\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Files\app\Traits\HasFile;
use LaravelEnso\Files\app\Traits\FilePolicies;
use LaravelEnso\Files\app\Contracts\Attachable;
use LaravelEnso\Files\app\Services\UploadManager;
use LaravelEnso\Files\app\Contracts\AuthorizesFileAcces;

class Upload extends Model implements Attachable, AuthorizesFileAcces
{
    use HasFile, FilePolicies;

    protected $optimizeImages = true;

    public function store(array $files)
    {
        return (new UploadManager($this, $files))
            ->handle();
    }
}
