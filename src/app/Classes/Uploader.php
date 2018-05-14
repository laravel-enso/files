<?php

namespace LaravelEnso\FileManager\app\Classes;

use Illuminate\Http\UploadedFile;
use LaravelEnso\FileManager\app\Exceptions\FileUploadException;

class Uploader
{
    private $files;
    private $path;
    private $tempPath;
    private $disk;
    private $validExtensions = [];
    private $validMimeTypes = [];

    public function __construct(string $path, string $tempPath, string $disk)
    {
        $this->path = $path;
        $this->tempPath = $tempPath;
        $this->disk = $disk;
        $this->files = collect();
    }

    public function start(array $files)
    {
        collect($files)->each(function ($file) {
            $this->upload($file);
        });
    }

    public function commit()
    {
        $this->files->each(function ($file) {
            \Storage::disk($this->disk)->move(
                $this->tempPath.DIRECTORY_SEPARATOR.$file['saved_name'],
                $this->path.DIRECTORY_SEPARATOR.$file['saved_name']
            );
        });
    }

    public function files()
    {
        return $this->files;
    }

    private function upload(UploadedFile $file)
    {
        $this->validate($file)
            ->uploadToTemp($file);
    }

    private function uploadToTemp(UploadedFile $file)
    {
        $savedName = $file->hashName();
        $tempPath = storage_path('app'.DIRECTORY_SEPARATOR.$this->tempPath);
        $file->move($tempPath, $savedName);

        $this->files->push([
            'original_name' => $file->getClientOriginalName(),
            'saved_name' => $savedName,
            'size' => \File::size($tempPath.DIRECTORY_SEPARATOR.$savedName),
            'full_path' => $tempPath.DIRECTORY_SEPARATOR.$savedName,
        ]);
    }

    public function deleteTempFiles()
    {
        $this->files->each(function ($file) {
            $fileWithPath = config('enso.config.paths.temp')
                .DIRECTORY_SEPARATOR.$file['saved_name'];

            return \Storage::has($fileWithPath)
                ? \Storage::disk($this->disk)
                    ->delete($fileWithPath)
                : null;
        });
    }

    public function setValidExtensions(array $extensions)
    {
        $this->validExtensions = $extensions;
    }

    public function setValidMimeTypes(array $mimeTypes)
    {
        $this->validMimeTypes = $mimeTypes;
    }

    private function validate(UploadedFile $file)
    {
        if ($file->isValid()) {
            $this->validateExtension($file)
                ->validateMimeType($file);

            return $this;
        }

        $this->deleteTempFiles();

        throw new FileUploadException(__(
            'Error uploading file :name',
            ['name' => $file->getClientOriginalName()]
        ));
    }

    private function validateExtension(UploadedFile $file)
    {
        if (empty($this->validExtensions) || $this->extensionIsValid($file)) {
            return $this;
        }

        $this->deleteTempFiles();

        throw new FileUploadException(__(
            'Extension :ext is not allowed. Valid extensions are :exts',
            [
                'ext' => $file->getClientOriginalExtension(),
                'exts' => implode(', ', $this->validExtensions),
            ]
        ));
    }

    private function extensionIsValid(UploadedFile $file)
    {
        return collect($this->validExtensions)
            ->contains($file->getClientOriginalExtension());
    }

    private function validateMimeType(UploadedFile $file)
    {
        if (empty($this->validMimeTypes) || $this->mimeTypeIsValid($file)) {
            return $this;
        }

        $this->deleteTempFiles();

        throw new FileUploadException(__(
            'Mime type :mime not allowed. Allowed mime types are :mimes',
            [
                'mime' => $file->getClientMimeType(),
                'mimes' => implode(', ', $this->validMimeTypes),
            ]
        ));
    }

    private function mimeTypeIsValid(UploadedFile $file)
    {
        return collect($this->validMimeTypes)
            ->contains($file->getClientMimeType());
    }
}
