<?php

namespace LaravelEnso\Files\app\Traits;

use LaravelEnso\Files\app\Models\Upload;

trait Uploads
{
    public function uploads()
    {
        return $this->hasMany(Upload::class, 'created_by');
    }
}
