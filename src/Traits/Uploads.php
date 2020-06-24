<?php

namespace LaravelEnso\Files\Traits;

use LaravelEnso\Files\Models\Upload;

trait Uploads
{
    public function uploads()
    {
        return $this->hasMany(Upload::class, 'created_by');
    }
}
