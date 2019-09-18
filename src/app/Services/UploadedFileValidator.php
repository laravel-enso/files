<?php

namespace LaravelEnso\Files\app\Services;

use LaravelEnso\Files\app\Exceptions\FileException;

class UploadedFileValidator extends FileValidator
{
    public function handle()
    {
        $this->validateFile();

        parent::handle();
    }

    private function validateFile()
    {
        if (! $this->file->isValid()) {
            throw FileException::uploadError($this->file->getClientOriginalName());
        }

        return $this;
    }
}
