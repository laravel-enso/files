<?php

namespace LaravelEnso\Files\app\Services;

use LaravelEnso\Files\app\Exceptions\FileUploadException;

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
            throw new FileUploadException(__(
                'Error uploading file :name',
                ['name' => $this->file->getClientOriginalName()]
            ));
        }

        return $this;
    }
}
