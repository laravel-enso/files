<?php

/**
 * Created by PhpStorm.
 * User: mihai
 * Date: 07.06.2017
 * Time: 11:57.
 */
use App\User;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\FileManager\Classes\FileManager;
use Tests\TestCase;

class FileManagerTest extends TestCase
{
    private $testFilePath;
    private $basePath;

    protected function setUp()
    {
        parent::setUp();

        $this->basePath = __DIR__.'/../testFiles/';
        $this->testFilePath = $this->basePath.'test_file.txt';
    }

    protected function tearDown()
    {
        $files = Storage::files(config('laravel-enso.paths.temp'));
        $this->excludeGitIgnore($files);
        Storage::delete($files);
    }

    /** @test */
    public function can_start_multiple_upload()
    {
        $fileManager = new FileManager(config('laravel-enso.paths.files'));

        $createdFiles = collect();
        for ($i = 0; $i < 3; $i++) {
            $currentOriginalFileName = 'test_file_'.$i.'.txt';
            $file = $this->createUploadedFile($this->testFilePath, $currentOriginalFileName, 'text/plain');
            $createdFiles->put($currentOriginalFileName, $file);
        }

        $fileManager->startUpload($createdFiles->toArray());

        $this->assertEquals(3, $fileManager->uploadedFiles->count());
        $this->assertEquals('success', $fileManager->getStatus()->level);
    }

    /** @test */
    public function can_start_single_upload()
    {
        $fileManager = new FileManager(config('laravel-enso.paths.files'));

        $currentOriginalFileName = 'test_file.txt';
        $file = $this->createUploadedFile($this->testFilePath, $currentOriginalFileName, 'text/plain');

        $fileManager->startSingleFileUpload($file);

        $this->assertEquals(1, $fileManager->uploadedFiles->count());
        $this->assertEquals('success', $fileManager->getStatus()->level);
    }

    /** @test */
    public function can_commit_upload_and_delete()
    {
        $fileManager = new FileManager(config('laravel-enso.paths.files'));
        $currentOriginalFileName = 'test_file.txt';
        $file = $this->createUploadedFile($this->testFilePath, $currentOriginalFileName, 'text/plain');

        //test commit
        $fileManager->startSingleFileUpload($file);
        $fileManager->commitUpload();

        $this->assertEquals('success', $fileManager->getStatus()->level);

        //test delete
        $savedFileName = $fileManager->uploadedFiles->first()['saved_name'];
        $fileManager->delete($savedFileName);
        $this->assertEquals('success', $fileManager->getStatus()->level);
    }

    /** @test */
    public function can_get_uploaded_file()
    {
        $fileManager = new FileManager(config('laravel-enso.paths.files'));
        $currentOriginalFileName = 'test_file.txt';
        $file = $this->createUploadedFile($this->testFilePath, $currentOriginalFileName, 'text/plain');

        $fileManager->startSingleFileUpload($file);
        $fileManager->commitUpload();

        //test get
        $savedFileName = $fileManager->uploadedFiles->first()['saved_name'];
        $fileWrapper = $fileManager->getFile($savedFileName);

        $this->assertEquals('text/plain', $fileWrapper->mimeType);
        $this->assertEquals(file_get_contents($this->testFilePath), $fileWrapper->file); //file = contents of the file

        $savedFileName = $fileManager->uploadedFiles->first()['saved_name'];
        $fileManager->delete($savedFileName);
    }

    /********************* private helpers ************************/

    private function createUploadedFile($filePath, $originalName, $mimeType = null)
    {
        $temporaryFilePath = $this->createTempFile($this->testFilePath);

        //this file gets automatically deleted at the end of the test by Laravel
        $file = new \Illuminate\Http\UploadedFile($temporaryFilePath, $originalName, $mimeType,
            filesize($temporaryFilePath), null, true);

        return $file;
    }

    private function createTempFile($path)
    {
        $randomPrefix = mt_rand(100, 1000);
        $tempFilePath = $this->basePath.$randomPrefix.'temp.txt';
        copy($path, $tempFilePath);

        return $tempFilePath;
    }

    private function excludeGitIgnore(&$files)
    {
        foreach ($files as $index => $file) {
            if (str_contains($file, '.gitignore')) {
                unset($files[$index]);
            }
        }
    }
}
