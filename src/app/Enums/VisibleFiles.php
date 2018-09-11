<?php

namespace LaravelEnso\FileManager\app\Enums;

use LaravelEnso\Helpers\app\Classes\Enum;

class VisibleFiles extends Enum
{
    public static function attributes()
    {
        return array_flip(config('enso.files.visible'));
    }
}
