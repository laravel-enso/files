<?php

namespace LaravelEnso\Files;

use LaravelEnso\Files\App\Models\Upload;

class UploadServiceProvider extends FileServiceProvider
{
    public $register = [
        'uploads' => [
            'model' => 'upload',
            'order' => 100,
        ],
    ];
}
