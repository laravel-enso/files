<?php

namespace LaravelEnso\Files\Services;

use Illuminate\Support\Facades\Validator;
use LaravelEnso\Files\Exceptions\File as Exception;
use LaravelEnso\ImageTransformer\Services\ImageTransformer;
use Symfony\Component\HttpFoundation\File\File;

class Process
{
    private ImageTransformer $transformer;

    private ?int $width;
    private ?int $height;
    private bool $optimize;

    public function __construct(
        private File $file,
    ) {
        $this->width = null;
        $this->height = null;
        $this->optimize = false;
    }

    public function width(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function height(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function optimize(): self
    {
        $this->optimize = true;

        return $this;
    }

    public function handle(): void
    {
        $this->validate();

        $transformer = new ImageTransformer($this->file);

        if ($this->width) {
            $transformer->width($this->width);
        }

        if ($this->height) {
            $transformer->height($this->height);
        }

        if ($this->optimize) {
            $transformer->optimize();
        }
    }

    private function validate(): void
    {
        $validator = Validator::make(
            ['file' => $this->file],
            ['file' => 'image']
        );

        if ($validator->fails()) {
            throw Exception::invalidImage($this->file);
        }

        $mimeTypes = implode(',', ImageTransformer::SupportedMimeTypes);

        $validator = Validator::make(
            ['file' => $this->file],
            ['file' => 'mimetypes:'.$mimeTypes]
        );

        if ($validator->fails()) {
            throw Exception::mimeType($this->file->getMimeType(), $mimeTypes);
        }
    }
}
