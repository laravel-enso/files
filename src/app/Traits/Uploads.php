<?php

namespace LaravelEnso\FileManager\app\Traits;

use LaravelEnso\FileManager\app\Models\Upload;

trait Uploads
{
    public function uploads()
    {
        return $this->hasMany(Upload::class, 'created_by');
    }
}
