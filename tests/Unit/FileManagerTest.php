<?php

namespace Tests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\FileManager\Classes\FileManager;

class FileManagerTest extends TestCase
{
    private $fileManager;
    private $files;

    protected function setUp()
    {
        parent::setUp();

        $this->fileManager = new FileManager('uploadTest', config('laravel-enso.paths.temp'));
        $this->files = [
            'firstFile'  => UploadedFile::fake()->image('picture.png'),
            'secondFile' => UploadedFile::fake()->create('document.doc'),
        ];
    }

    /** @test */
    public function upload_files_to_temp()
    {
        $this->fileManager->startUpload($this->files);
        $uploadedFiles = $this->fileManager->getUploadedFiles();

        $this->assertEquals(2, $uploadedFiles->count());

        $uploadedFiles->each(function ($file) {
            Storage::assertExists('temp/'.$file['saved_name']);
        });

        $this->fileManager->deleteTempFiles();

        $uploadedFiles->each(function ($file) {
            Storage::assertMissing('temp/'.$file['saved_name']);
        });
    }

    /** @test */
    public function commit_upload()
    {
        $this->fileManager->startUpload($this->files)->commitUpload();
        $uploadedFiles = $this->fileManager->getUploadedFiles();

        $uploadedFiles->each(function ($file) {
            Storage::assertExists('uploadTest/'.$file['saved_name']);
        });

        $this->cleanUp();
    }

    /** @test */
    public function getInline()
    {
        $this->fileManager->startUpload($this->files)->commitUpload();
        $uploadedFile = $this->fileManager->getUploadedFiles()->first();
        $response = $this->fileManager->getInline($uploadedFile['original_name'], $uploadedFile['saved_name']);

        $this->assertEquals(200, $response->status());

        $this->cleanUp();
    }

    /** @test */
    public function download()
    {
        $this->fileManager->startUpload($this->files)->commitUpload();
        $uploadedFile = $this->fileManager->getUploadedFiles()->first();
        $response = $this->fileManager->download($uploadedFile['original_name'], $uploadedFile['saved_name']);

        $this->assertEquals(200, $response->status());

        $this->cleanUp();
    }

    private function cleanUp()
    {
        Storage::deleteDirectory('uploadTest');
    }
}
