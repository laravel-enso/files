<?php

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use LaravelEnso\Core\app\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\FileManager\app\Traits\HasFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\FileManager\app\Classes\FileManager;
use LaravelEnso\FileManager\app\Contracts\Attachable;
use LaravelEnso\FileManager\app\Exceptions\FileUploadException;

class FileManagerTest extends TestCase
{
    use RefreshDatabase;

    private $testModel;
    private $file;

    protected function setUp(): void
    {
        parent::setUp();

        // $this->withoutExceptionHandling();

        $this->seed()
            ->actingAs(User::first());

        $this->file = UploadedFile::fake()->image('picture.png');
        $this->testModel = $this->model();
    }

    public function tearDown(): void
    {
        $this->cleanUp();
        parent::tearDown();
    }

    /** @test */
    public function can_upload_file()
    {
        $this->testModel->upload($this->file);

        $this->assertNotNull($this->testModel->file);

        Storage::assertExists(
            FileManager::TestingFolder.DIRECTORY_SEPARATOR.$this->testModel->file->saved_name
        );
    }

    /** @test */
    public function cant_upload_file_with_invalid_extension()
    {
        $manager = new FileManager($this->testModel);

        $this->expectException(FileUploadException::class);

        $manager->file($this->file)
            ->extensions(['jpg'])
            ->upload();
    }

    /** @test */
    public function cant_upload_file_with_invalid_mime_type()
    {
        $manager = new FileManager($this->testModel);

        $this->expectException(FileUploadException::class);

        $manager->file($this->file)
            ->mimeTypes(['application/msword'])
            ->upload();
    }

    /** @test */
    public function can_display_file_inline()
    {
        $manager = new FileManager($this->testModel);

        $manager->file($this->file)
            ->upload();

        $response = $manager->inline($this->file->hashname());

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function can_download_file()
    {
        $manager = new FileManager($this->testModel);

        $manager->file($this->file)
            ->upload();

        $response = $manager->download(
            $this->testModel->file->original_name,
            $this->testModel->file->saved_name
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    private function model()
    {
        $this->createTestTable();

        return AttachableModel::create();
    }

    private function createTestTable()
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
