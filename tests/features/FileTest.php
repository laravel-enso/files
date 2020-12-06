<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\Core\Models\User;
use LaravelEnso\Files\Contracts\Attachable;
use LaravelEnso\Files\Exceptions\File;
use LaravelEnso\Files\Traits\HasFile;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    private $model;
    private $file;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs(User::first());

        $this->file = UploadedFile::fake()->image('picture.png');
        $this->createTestTable();
        $this->model = AttachableModel::create();
    }

    public function tearDown(): void
    {
        $this->cleanUp();
        parent::tearDown();
    }

    /** @test */
    public function can_upload_file()
    {
        $this->model->file->upload($this->file);

        $this->assertNotNull($this->model->file);

        Storage::assertExists($this->model->file->path);
    }

    /** @test */
    public function can_attach_file()
    {
        $folder = Config::get('enso.files.testingFolder');
        $filename = 'test.txt';
        Storage::put("{$folder}/$filename", 'test');
        $this->model->file->attach("{$folder}/$filename", $filename);

        $this->assertNotNull($this->model->file);

        Storage::assertExists($this->model->file->path);
    }

    /** @test */
    public function cant_upload_file_with_invalid_extension()
    {
        $file = UploadedFile::fake()->image('picture.jpg');

        $this->expectException(File::class);

        $this->expectExceptionMessage(
            File::invalidExtension($file->getClientOriginalExtension(), 'png')
                ->getMessage()
        );

        $this->model->file->upload($file);
    }

    /** @test */
    public function cant_upload_file_with_invalid_mime_type()
    {
        $file = UploadedFile::fake()->create('doc.png', 0, 'application/msword');

        $this->expectException(File::class);

        $this->expectExceptionMessage(
            File::invalidMimeType($file->getMimeType(), 'image/png')
                ->getMessage()
        );

        $this->model->file->upload($file);
    }

    /** @test */
    public function can_display_file_inline()
    {
        $this->model->file->upload($this->file);

        $response = $this->model->file->inline($this->file->hashname());

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function can_download_file()
    {
        $this->model->file->upload($this->file);

        $response = $this->model->file->download();

        $this->assertEquals(200, $response->getStatusCode());
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
