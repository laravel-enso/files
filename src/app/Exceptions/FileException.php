<?php

namespace LaravelEnso\Files\app\Exceptions;

use LaravelEnso\Helpers\app\Exceptions\EnsoException;

class FileException extends EnsoException
{
    public static function duplicates($files)
    {
        return new static(__(
            'File(s) :files already uploaded for this entity',
            ['files' => $files]
        ));
    }

    public static function uploadError($file)
    {
        return new static(__(
            'Error uploading file :name',
            ['name' => $file->getClientOriginalName()]
        ));
    }

    public static function invalidExtension($extension, $allowed)
    {
        return new static(__(
            'Extension :extension is not allowed. Valid extensions are :allowed',
            ['extension' => $extension, 'allowed' => $allowed]
        ));
    }

    public static function invalidMimeType($mime, $allowed)
    {
        return new static(__(
            'Mime type :mime not allowed. Allowed mime types are :allowed',
            ['mime' => $mime, 'allowed' => $allowed]
        ));
    }
}
