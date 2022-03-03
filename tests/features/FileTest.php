<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\Files\Contracts\Attachable;
use LaravelEnso\Files\Contracts\Extensions;
use LaravelEnso\Files\Contracts\MimeTypes;
use LaravelEnso\Files\Exceptions\File as Exception;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Users\Models\User;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    private AttachableModel $model;
    private UploadedFile $file;

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
        $file = File::upload($this->model, $this->file);
        $this->model->file()->associate($file)->save();

        $this->assertNotNull($this->model->file);

        Storage::assertExists($this->model->file->path());
    }

    /** @test */
    public function can_attach_file()
    {
        $folder = Config::get('enso.files.testingFolder');
        $filename = 'test.txt';

        Storage::put("{$folder}/{$filename}", 'test');

        $file = File::attach($this->model, $filename, $filename);

        $this->model->file()->associate($file)->save();

        $this->assertNotNull($this->model->file);

        Storage::assertExists($this->model->file->path());
    }

    /** @test */
    public function cant_upload_file_with_invalid_extension()
    {
        $file = UploadedFile::fake()->image('image.jpg');

        $this->expectException(Exception::class);

        $this->expectExceptionMessage(
            Exception::invalidExtension($file->getClientOriginalExtension(), 'png')
                ->getMessage()
        );

        File::upload($this->model, $file);
    }

    /** @test */
    public function cant_upload_file_with_invalid_mime_type()
    {
        $file = UploadedFile::fake()->create('doc.doc', 0, 'application/msword');

        $this->expectException(Exception::class);

        $this->expectExceptionMessage(
            Exception::invalidMimeType($file->getMimeType(), 'image/png')
                ->getMessage()
        );

        $this->model->extension = 'doc';
        File::upload($this->model, $file);
    }

    /** @test */
    public function can_display_file_inline()
    {
        $file = File::upload($this->model, $this->file);
        $this->model->file()->associate($file)->save();

        $response = $this->model->file->inline($this->file->hashname());

        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function can_download_file()
    {
        $file = File::upload($this->model, $this->file);
        $this->model->file()->associate($file)->save();

        $response = $this->model->file->download();

        $this->assertEquals(200, $response->getStatusCode());
    }

    private function createTestTable(): self
    {
        Schema::create('attachable_models', function ($table) {
            $table->increments('id');

            $table->unsignedBigInteger('file_id')->nullable();
            $table->foreign('file_id')->references('id')->on('files')
                ->onUpdate('restrict')->onDelete('cascade');

            $table->timestamps();
        });

        return $this;
    }

    private function cleanUp()
    {
        Storage::deleteDirectory(Config::get('enso.files.testingFolder'));
    }
}

class AttachableModel extends Model implements Attachable, Extensions, MimeTypes
{
    public string $extension = 'png';

    public function file(): Relation
    {
        return $this->belongsTo(File::class);
    }

    public function extensions(): array
    {
        return [$this->extension];
    }

    public function mimeTypes(): array
    {
        return ['image/png'];
    }
}
