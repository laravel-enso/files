<?php

namespace LaravelEnso\Files\app\Services;

use LaravelEnso\Files\app\Exceptions\FileException;
use Symfony\Component\HttpFoundation\File\File;

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
            throw FileException::invalidExtension(
                $this->file->getClientOriginalExtension(),
                implode(', ', $this->extensions)
            );
        }

        return $this;
    }

    private function validateMimeType()
    {
        if (collect($this->mimeTypes)->isNotEmpty() &&
            ! collect($this->mimeTypes)
                ->contains($this->file->getClientMimeType())) {
            throw FileException::invalidMimeType(
                $this->file->getClientMimeType(),
                implode(', ', $this->mimeTypes),
            );
        }

        return $this;
    }
}
