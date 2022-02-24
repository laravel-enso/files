<?php

namespace LaravelEnso\Files\Exceptions;

use Illuminate\Http\File as IlluminateFile;
use Illuminate\Http\UploadedFile;
use LaravelEnso\Helpers\Exceptions\EnsoException;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class File extends EnsoException
{
    public static function duplicates($files)
    {
        return new static(__(
            'File(s) :files already uploaded for this entity',
            ['files' => $files]
        ));
    }

    public static function attach(IlluminateFile $file)
    {
        return new static(__(
            'Error attaching file :name',
            ['name' => $file->getBasename()]
        ));
    }

    public static function upload(UploadedFile $file)
    {
        return new static(__(
            'Error uploading file :name',
            ['name' => $file->getClientOriginalName()]
        ));
    }

    public static function extension(string $extension, string $allowed)
    {
        return new static(__(
            'Extension :extension is not allowed. Valid extensions are :allowed',
            ['extension' => $extension, 'allowed' => $allowed]
        ));
    }

    public static function mimeType(string $mime, string $allowed)
    {
        return new static(__(
            'Mime type :mime not allowed. Allowed mime types are :allowed',
            ['mime' => $mime, 'allowed' => $allowed]
        ));
    }

    public static function invalidImage(SymfonyFile $file)
    {
        return new static(__(
            'Invalid image :name',
            ['name' => $file->getBasename()]
        ));
    }

    //TODO remove
    public static function uploadError($file)
    {
        return new static(__(
            'Error uploading file :name',
            ['name' => $file->getClientOriginalName()]
        ));
    }

    //TODO remove
    public static function invalidExtension($extension, $allowed)
    {
        return new static(__(
            'Extension :extension is not allowed. Valid extensions are :allowed',
            ['extension' => $extension, 'allowed' => $allowed]
        ));
    }

    //TODO remove
    public static function invalidMimeType($mime, $allowed)
    {
        return new static(__(
            'Mime type :mime not allowed. Allowed mime types are :allowed',
            ['mime' => $mime, 'allowed' => $allowed]
        ));
    }
}
