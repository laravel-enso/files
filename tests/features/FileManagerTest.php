<?php

use LaravelEnso\Core\app\Models\User;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\TestHelper\app\Traits\SignIn;
use LaravelEnso\FileManager\app\Traits\HasFile;
use LaravelEnso\FileManager\app\Classes\FileManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\FileManager\app\Contracts\Attachable;
use LaravelEnso\FileManager\app\Exceptions\FileUploadException;

class FileManagerTest extends TestCase
{
    use RefreshDatabase, SignIn;

    private $model;
    private $file;

    protected function setUp()
    {
        parent::setUp();

        // $this->withoutExceptionHandling();

        $this->seed()
            ->createAttachableModelsTable()
            ->signIn(User::first());

        $this->file = UploadedFile::fake()->image('picture.png');
        $this->model = AttachableModel::create();
    }

    /** @test */
    public function upload()
    {
        $this->model->upload($this->file);

        $this->assertNotNull($this->model->file);

        Storage::assertExists(
            FileManager::TestingFolder.DIRECTORY_SEPARATOR.$this->model->file->saved_name
        );

        $this->cleanUp();
    }

    /** @test */
    public function cant_upload_file_with_invalid_extension()
    {
        $manager = new FileManager($this->model);

        $this->expectException(FileUploadException::class);

        $manager->file($this->file)
            ->extensions(['jpg'])
            ->upload();
    }

    /** @test */
    public function cant_upload_file_with_invalid_mime_type()
    {
        $manager = new FileManager($this->model);

        $this->expectException(FileUploadException::class);

        $manager->file($this->file)
            ->mimeTypes(['application/msword'])
            ->upload();
    }

    /** @test */
    public function inline()
    {
        $manager = new FileManager($this->model);

        $manager->file($this->file)
            ->upload();

        $response = $manager->inline($this->file->hashname());

        $this->assertEquals(200, $response->getStatusCode());

        $this->cleanUp();
    }

    /** @test */
    public function download()
    {
        $manager = new FileManager($this->model);

        $manager->file($this->file)
            ->upload();

        $response = $manager->download($this->model->file->original_name, $this->model->file->saved_name);

        $this->assertEquals(200, $response->getStatusCode());

        $this->cleanUp();
    }

    private function createAttachableModelsTable()
    {
        Schema::create('attachable_models', function ($table) {
            $table->increments('id');
            $table->timestamps();
        });

        return $this;
    }

    private function cleanUp()
    {
        \Storage::deleteDirectory(FileManager::TestingFolder);
    }
}

class AttachableModel extends Model implements Attachable
{
    use HasFile;

    protected $mimeTypes = ['image/png'];
    protected $extensions = ['png'];
}
