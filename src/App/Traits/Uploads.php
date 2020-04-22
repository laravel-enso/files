<?php

namespace LaravelEnso\Files\App\Traits;

use LaravelEnso\Files\App\Models\Upload;

trait Uploads
{
    public function uploads()
    {
        return $this->hasMany(Upload::class, 'created_by');
    }
}
