<?php

namespace LaravelEnso\Files;

class UploadServiceProvider extends FileServiceProvider
{
    public $register = [
        'uploads' => [
            'model' => 'upload',
            'order' => 100,
        ],
    ];
}
