<?php

namespace LaravelEnso\Files\Services;

use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use LaravelEnso\Files\Exceptions\File as Exception;

class Validate
{
    private ?array $extensions;
    private ?array $mimeTypes;

    public function __construct(
        protected File|UploadedFile $file
    ) {
        $this->extensions = null;
        $this->mimeTypes = null;
    }

    public function extensions(array $extensions)
    {
        $this->extensions = $extensions;
    }

    public function mimeTypes(array $mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
    }

    public function handle(): void
    {
        $this->file()
            ->extension()
            ->mimeType();
    }

    private function file(): self
    {
        if ($this->file instanceof File) {
            if (! $this->file->isReadable()) {
                throw Exception::attach($this->file);
            }
        } elseif (! $this->file->isValid()) {
            throw Exception::upload($this->file);
        }

        return $this;
    }

    private function extension(): self
    {
        $valid = new Collection($this->extensions);
        $extension = $this->file instanceof UploadedFile
            ? $this->file->getClientOriginalExtension()
            : $this->file->extension();
        $shouldThrow = $valid->isNotEmpty() && $valid->doesntContain($extension);

        if ($shouldThrow) {
            throw Exception::extension($extension, $valid->implode(','));
        }

        return $this;
    }

    private function mimeType(): self
    {
        $valid = new Collection($this->mimeTypes);
        $mimeType = $this->file->getMimeType();
        $shouldThrow = $valid->isNotEmpty() && $valid->doesntContain($mimeType);

        if ($shouldThrow) {
            throw Exception::mimeType($mimeType, $valid->implode(','));
        }

        return $this;
    }
}
