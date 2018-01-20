<?php

namespace LaravelEnso\FileManager\app\Classes;

abstract class Handler
{
    private $manager;

    public function __construct()
    {
        $this->manager = new Manager($this->path());
    }

    protected function inline(string $file)
    {
        return $this->manager->getInline($file);
    }

    protected function startUpload(array $file)
    {
        $this->manager->startUpload($file);
    }

    protected function commitUpload()
    {
        $this->manager->commitUpload();
    }

    protected function uploadedFiles()
    {
        return $this->manager->uploadedFiles();
    }

    protected function deleteTempFiles()
    {
        $this->manager->deleteTempFiles();
    }

    protected function delete(string $file)
    {
        $this->manager->delete($file);
    }

    abstract protected function path();

    protected function tempPath(string $path)
    {
        $this->manager->tempPath($path);
    }
}
