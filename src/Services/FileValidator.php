<?php

namespace LaravelEnso\Files\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use LaravelEnso\Files\Exceptions\File;
use LaravelEnso\Files\Exceptions\File as FileException;
use Symfony\Component\HttpFoundation\File\File as BaseFile;

class FileValidator
{
    protected BaseFile $file;

    private array $extensions;
    private array $mimeTypes;

    public function __construct(BaseFile $file, array $extensions, array $mimeTypes)
    {
        $this->file = $file;
        $this->extensions = $extensions;
        $this->mimeTypes = $mimeTypes;
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
        $valid = (new Collection($this->extensions));

        $extension = $this->file instanceof UploadedFile
            ? $this->file->getClientOriginalExtension()
            : $this->file->getExtension();

        if ($valid->isNotEmpty() && ! $valid->contains($extension)) {
            throw FileException::invalidExtension(
                $extension,
                implode(', ', $this->extensions)
            );
        }

        return $this;
    }

    private function validateMimeType()
    {
        $valid = (new Collection($this->mimeTypes));

        $mimeType = $this->file->getMimeType();

        if ($valid->isNotEmpty() && ! $valid->contains($mimeType)) {
            throw FileException::invalidMimeType(
                $mimeType,
                implode(', ', $this->mimeTypes),
            );
        }

        return $this;
    }
}
