<?php

use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use LaravelEnso\Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\Files\Traits\HasFile;
use LaravelEnso\Files\Contracts\Attachable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Files\Exceptions\File;

class WebOperationsTest extends TestCase
{
    use RefreshDatabase;

    private $testModel;
    private $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

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
            $this->testModel->folder().DIRECTORY_SEPARATOR.$this->testModel->file->saved_name
        );
    }

    /** @test */
    public function cant_upload_file_with_invalid_extension()
    {
        $invalidExtensionFile = UploadedFile::fake()->image('picture.jpg');

        $this->expectException(File::class);

        $this->expectExceptionMessage(
            File::invalidExtension(
                $invalidExtensionFile->getClientOriginalExtension(),
                'png'
            )->getMessage()
        );

        $this->testModel->upload($invalidExtensionFile);
    }

    /** @test */
    public function cant_upload_file_with_invalid_mime_type()
    {
        $invalidMimeFile = UploadedFile::fake()->create('doc.png', 0, 'application/msword');

        $this->expectException(File::class);

        $this->expectExceptionMessage(
            File::invalidMimeType(
                $invalidMimeFile->getMimeType(),
                'image/png'
            )->getMessage()
        );

       $this->testModel->upload($invalidMimeFile);
    }

    /** @test */
    public function can_display_file_inline()
    {
        $this->testModel->upload($this->file);

        $response = $this->testModel->inline($this->file->hashname());

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function can_download_file()
    {
        $this->testModel->upload($this->file);

        $response = $this->testModel->download();

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
        Storage::deleteDirectory(Config::get('enso.files.testingFolder'));
    }
}

class AttachableModel extends Model implements Attachable
{
    use HasFile;

    protected $mimeTypes = ['image/png'];
    protected $extensions = ['png'];
}
