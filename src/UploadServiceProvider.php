<?php

namespace LaravelEnso\Files;

use LaravelEnso\Files\Models\Upload;

class UploadServiceProvider extends FileServiceProvider
{
    public function folders(): array
    {
        return ['uploads' => [
            'model' => Upload::morphMapKey(),
            'order' => 80,
        ]];
    }
}
