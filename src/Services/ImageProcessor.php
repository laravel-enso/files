<?php

namespace LaravelEnso\Files\Services;

use Illuminate\Support\Facades\Validator;
use LaravelEnso\ImageTransformer\Services\ImageTransformer;
use Symfony\Component\HttpFoundation\File\File;

class ImageProcessor
{
    private File $file;
    private bool $optimize;
    private array $resize;
    private ImageTransformer $transformer;

    public function __construct(File $file, bool $optimize, array $resize)
    {
        $this->file = $file;
        $this->optimize = $optimize;
        $this->resize = $resize;
    }

    public function handle(): void
    {
        if ($this->isImage()) {
            if (! empty($this->resize)) {
                $this->transformer()
                    ->width($this->resize['width'])
                    ->height($this->resize['height']);
            }

            if ($this->optimize) {
                $this->transformer()->optimize();
            }
        }
    }

    private function isImage(): bool
    {
        return Validator::make(
            ['file' => $this->file],
            ['file' => 'image|mimetypes:'.implode(',', ImageTransformer::SupportedMimeTypes)]
        )->passes();
    }

    private function transformer()
    {
        return $this->transformer ??= new ImageTransformer($this->file);
    }
}
