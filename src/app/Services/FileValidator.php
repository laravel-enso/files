<?php

namespace LaravelEnso\Files\app\Services;

use Symfony\Component\HttpFoundation\File\File;
use LaravelEnso\Files\app\Exceptions\InvalidFileTypeException;
use LaravelEnso\Files\app\Exceptions\InvalidExtensionException;

class FileValidator
{
    private $extensions;
    private $mimeTypes;

    protected $file;

    public function __construct(File $file, array $extensions, array $mimeTypes)
    {
        $this->file = $file;
        $this->extensions = $extensions;
        $this->mimeTypes = $mimeTypes;
    }

    public function handle()
    {
        $this->validateExtension()
            ->validateMimeType();
    }

    private function validateExtension()
    {
        if (collect($this->extensions)->isNotEmpty() &&
            ! collect($this->extensions)
                ->contains($this->file->getClientOriginalExtension())) {
            throw new InvalidExtensionException(__(
                'Extension :ext is not allowed. Valid extensions are :exts', [
                    'ext' => $this->file->getClientOriginalExtension(),
                    'exts' => implode(', ', $this->extensions),
            ]));
        }

        return $this;
    }

    private function validateMimeType()
    {
        if (collect($this->mimeTypes)->isNotEmpty() &&
            ! collect($this->mimeTypes)
                ->contains($this->file->getClientMimeType())) {
            throw new InvalidFileTypeException(__(
                    'Mime type :mime not allowed. Allowed mime types are :mimes', [
                        'mime' => $this->file->getClientMimeType(),
                        'mimes' => implode(', ', $this->mimeTypes),
                ]));
        }

        return $this;
    }
}
