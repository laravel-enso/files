<?php

namespace LaravelEnso\FileManager\Classes;

use Illuminate\Http\UploadedFile;

class FileUploader
{
    private $files;
    private $filesPath;
    private $tempPath;
    private $disk;

    public function __construct(string $filesPath, string $tempPath, string $disk)
    {
        $this->filesPath = $filesPath;
        $this->tempPath = $tempPath;
        $this->disk = $disk;
        $this->files = collect();
    }

    public function start(array $files)
    {
        foreach ($files as $file) {
            $this->upload($file);
        }
    }

    public function commit()
    {
        $this->files->each(function ($file) {
            \Storage::disk($this->disk)->move(config('laravel-enso.paths.temp').DIRECTORY_SEPARATOR.$file['saved_name'],
                $this->filesPath.DIRECTORY_SEPARATOR.$file['saved_name']);
        });
    }

    public function getFiles()
    {
        return $this->files;
    }

    private function upload(UploadedFile $file)
    {
        if (!$file->isValid()) {
            throw new \EnsoException('Error Processing File:'.$file->getClientOriginalName(), 409);
        }

        $this->uploadToTemp($file);
    }

    private function uploadToTemp($file)
    {
        $fileName = $file->getClientOriginalName();
        $savedName = $file->hashName();
        $fileSize = $file->getClientSize();
        $file->move(storage_path('app'.DIRECTORY_SEPARATOR.$this->tempPath), $savedName);

        $this->files->push([
            'original_name' => $fileName,
            'saved_name'    => $savedName,
            'size'          => $fileSize,
        ]);
    }

    public function deleteTempFiles()
    {
        $this->files->each(function ($file) {
            $fileWithPath = config('laravel-enso.paths.temp').DIRECTORY_SEPARATOR.$file['saved_name'];

            return (\Storage::has($fileWithPath)) ? \Storage::disk($this->disk)->delete($fileWithPath) : null;
        });
    }
}
