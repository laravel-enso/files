<?php

namespace LaravelEnso\Files\App\Services;

use LaravelEnso\Files\App\Exceptions\File;

class UploadedFileValidator extends FileValidator
{
    public function handle(): void
    {
        $this->validateFile();

        parent::handle();
    }

    private function validateFile(): self
    {
        if (! $this->file->isValid()) {
            throw File::uploadError($this->file->getClientOriginalName());
        }

        return $this;
    }
}
