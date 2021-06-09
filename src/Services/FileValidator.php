<?php

namespace LaravelEnso\Files\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use LaravelEnso\Files\Exceptions\File;
use LaravelEnso\Files\Exceptions\File as FileException;
use Symfony\Component\HttpFoundation\File\File as BaseFile;

class FileValidator
{
    public function __construct(
        protected BaseFile $file,
        private array $extensions,
        private array $mimeTypes
    ) {
    }

    public function handle(): void
    {
        $this->validateFile()
            ->validateExtension()
            ->validateMimeType();
    }

    private function validateFile(): self
    {
        if (! $this->file->isValid()) {
            throw File::uploadError($this->file->getClientOriginalName());
        }

        return $this;
    }

    private function validateExtension(): self
    {
        $valid = new Collection($this->extensions);

        $extension = $this->file instanceof UploadedFile
            ? $this->file->getClientOriginalExtension()
            : $this->file->getExtension();

        if ($valid->isNotEmpty() && ! $valid->contains($extension)) {
            $extensions = implode(', ', $this->extensions);
            throw FileException::invalidExtension($extension, $extensions);
        }

        return $this;
    }

    private function validateMimeType()
    {
        $valid = new Collection($this->mimeTypes);

        $mimeType = $this->file->getMimeType();

        if ($valid->isNotEmpty() && ! $valid->contains($mimeType)) {
            $mimeTypes = implode(', ', $this->mimeTypes);
            throw FileException::invalidMimeType($mimeType, $mimeTypes);
        }

        return $this;
    }
}
