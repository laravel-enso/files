<?php

namespace LaravelEnso\Files;

use LaravelEnso\Files\Models\Upload;

class UploadServiceProvider extends FileServiceProvider
{
    public function boot()
    {
        $this->register['uploads'] = [
            'model' => Upload::morphMapKey(),
            'order' => 80,
        ];

        parent::boot();
    }
}
