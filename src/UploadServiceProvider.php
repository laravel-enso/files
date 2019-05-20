<?php

namespace LaravelEnso\Files;

use LaravelEnso\Files\app\Models\Upload;

class UploadServiceProvider extends FileServiceProvider
{
    public $register = [
        'uploads' => [
            'model' => Upload::class,
            'order' => 100,
        ]
    ];
}
