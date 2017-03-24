<?php

namespace LaravelEnso\FileManager\Classes;

use LaravelEnso\Helpers\Classes\AbstractObject;

class FileManagerStatus extends AbstractObject
{
    public $level;
    public $message;
    public $errors;

    public function __construct()
    {
        $this->errors = collect();
    }
}
