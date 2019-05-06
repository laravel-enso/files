<?php

namespace LaravelEnso\FileManager\app\Classes;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use LaravelEnso\FileManager\app\Contracts\Attachable;
use LaravelEnso\ImageTransformer\app\Classes\ImageTransformer;
use LaravelEnso\FileManager\app\Exceptions\FileUploadException;

class FileManager
{
    const TestingFolder = 'testing';

    private $model;
    private $file;
    private $isImage;
    private $disk;
    private $extensions;
    private $mimeTypes;
    private $optimize;
    private $resize;
    private $transformer;

    public function __construct(Attachable $model)
    {
        $this->model = $model;
        $this->disk = 'local';
    }

    public function inline()
    {
        return \Storage::response($this->uploadedFile());
    }

    public function download()
    {
        return \Storage::download(
            $this->uploadedFile(),
            Str::ascii($this->model->file->original_name)
        );
    }

    public function delete()
    {
        if (! $this->model->file) {
            return;
        }

        \DB::transaction(function () {
            \Storage::disk($this->disk)
                ->delete($this->uploadedFile());

            $this->model->file->delete();
        });
    }

    public function upload()
    {
        $this->validateFile()
            ->validateExtension()
            ->validateMimeType();

        if ($this->isImage) {
            if ($this->optimize) {
                $this->transformer()->optimize();
            }

            if ($this->resize) {
                $this->transformer()
                    ->width($this->resize[0])
                    ->height($this->resize[1]);
            }
        }

        $this->persist();
    }

    private function persist()
    {
        \DB::transaction(function () {
            $original_name = method_exists($this->file, 'getClientOriginalName')
                ? $this->file->getClientOriginalName()
                : $this->file->getBaseName();
            $this->model->file()->create([
                'original_name' => $original_name,
                'saved_name' => $this->file->hashName(),
                'size' => $this->file->getSize(),
                'mime_type' => $this->file->getMimeType(),
            ]);

            Container::getInstance()->make(FilesystemFactory::class)->disk($this->disk)->putFileAs(
                $this->folder(), $this->file, $this->file->hashName(), ['disk' => $this->disk]
            );
        });
    }

    private function uploadedFile()
    {
        return $this->folder()
            .DIRECTORY_SEPARATOR
            .$this->model->file->saved_name;
    }

    /**
     * @param \Illuminate\Http\UploadedFile|\Illuminate\Http\File $file
     * @return $this
     */
    public function file(File $file)
    {
        if (!$file instanceof \Illuminate\Http\File && !$file instanceof \Illuminate\Http\UploadedFile) {
            throw new \InvalidArgumentException('$file must be a File or UploadedFile object.');
        }
        $this->file = $file;

        $this->isImage = \Validator::make(
                ['file' => $file],
                ['file' => 'image|mimetypes:'.implode(',', $this->supportedMimeTypes())]
            )->passes();

        return $this;
    }

    private function transformer()
    {
        return $this->transformer
            ?? new ImageTransformer($this->file);
    }

    public function optimize($optimize)
    {
        $this->optimize = $optimize;

        return $this;
    }

    public function resize($size)
    {
        $this->resize = $size;

        return $this;
    }

    public function disk(string $disk)
    {
        $this->disk = $disk;

        return $this;
    }

    public function extensions(array $extensions)
    {
        $this->extensions = $extensions;

        return $this;
    }

    public function mimeTypes(array $mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;

        return $this;
    }

    private function validateFile()
    {
        if (method_exists($this->file, 'isValid') && ! $this->file->isValid()) {
            throw new FileUploadException(__(
                'Error uploading file :name',
                ['name' => $this->file->getClientOriginalName()]
            ));
        }

        return $this;
    }

    private function validateExtension()
    {
        if (empty($this->extensions) ||
            collect($this->extensions)
                ->contains($this->file->guessExtention())) {
            return $this;
        }

        throw new FileUploadException(__(
            'Extension :ext is not allowed. Valid extensions are :exts', [
                'ext' => $this->file->guessExtention(),
                'exts' => implode(', ', $this->extensions),
            ]));
    }

    private function validateMimeType()
    {
        if (empty($this->mimeTypes) ||
            collect($this->mimeTypes)
                ->contains($this->file->getMimeType())) {
            return $this;
        }

        throw new FileUploadException(__(
            'Mime type :mime not allowed. Allowed mime types are :mimes', [
                'mime' => $this->file->getMimeType(),
                'mimes' => implode(', ', $this->mimeTypes),
            ]));
    }

    private function supportedMimeTypes()
    {
        return ImageTransformer::SupportedMimeTypes;
    }

    private function folder()
    {
        return app()->environment('testing')
            ? self::TestingFolder
            : $this->model->folder();
    }
}
