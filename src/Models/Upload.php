<?php

namespace LaravelEnso\Files\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelEnso\Files\Contracts\Attachable;
use LaravelEnso\Files\Contracts\AuthorizesFileAccess;
use LaravelEnso\Files\Services\UploadManager;
use LaravelEnso\Files\Traits\FilePolicies;
use LaravelEnso\Files\Traits\HasFile;
use LaravelEnso\Helpers\Traits\CascadesMorphMap;

class Upload extends Model implements Attachable, AuthorizesFileAccess
{
    use CascadesMorphMap, HasFile, FilePolicies;

    protected $optimizeImages = true;

    protected $folder = 'files';

    public static function store(array $files)
    {
        return (new UploadManager($files))->handle();
    }
}
