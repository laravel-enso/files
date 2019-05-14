<?php

namespace LaravelEnso\Files\app\Services;

use Illuminate\Support\Facades\Validator;
use LaravelEnso\ImageTransformer\app\Services\ImageTransformer;

class ImageProcessor
{
    private $file;
    private $optimize;
    private $resize;
    private $transformer;

    public function __construct($file, $optimize, $resize)
    {
        $this->file = $file;
        $this->optimize = $optimize;
        $this->resize = $resize;
    }

    public function handle()
    {
        if ($this->isImage()) {
            if ($this->optimize) {
                $this->transformer()->optimize();
            }

            if (! empty($this->resize)) {
                $this->transformer()
                    ->width($this->resize['width'])
                    ->height($this->resize['height']);
            }
        }
    }

    private function isImage()
    {
        return Validator::make(
            ['file' => $this->file],
            ['file' => 'image|mimetypes:'.implode(',', ImageTransformer::SupportedMimeTypes)]
        )->passes();
    }

    private function transformer()
    {
        return $this->transformer
            ?? $this->transformer = new ImageTransformer($this->file);
    }
}
