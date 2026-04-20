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
use LaravelEnso\Files\Models\Favorite;
use LaravelEnso\Files\Models\File;
use LaravelEnso\Files\Models\Type;
use LaravelEnso\Users\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FileManagerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ManagedAttachableModel $model;
    private File $file;
    private Type $type;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs($this->user = User::first());

        $this->createTestTable();

        $this->type = Type::for(ManagedAttachableModel::class);
        $this->type->update(['is_browsable' => true]);

        $this->model = ManagedAttachableModel::create();
        $this->file = File::upload($this->model, UploadedFile::fake()->image('document.png'));
        $this->model->file()->associate($this->file)->save();
    }

    protected function tearDown(): void
    {
        File::query()->get()
            ->each(fn (File $file) => Storage::delete($file->path()));

        parent::tearDown();
    }

    #[Test]
    public function can_browse_recent_files_and_filter_favorites(): void
    {
        Favorite::create([
            'user_id' => $this->user->id,
            'file_id' => $this->file->id,
        ]);

        $query = ['query' => 'document'];

        $this->get(route('core.files.browse', ['type' => $this->type->id, ...$query], false))
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $this->file->id, 'name' => 'document']);

        $this->get(route('core.files.recent', $query, false))
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $this->file->id]);

        $this->get(route('core.files.favorites', $query, false))
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $this->file->id]);
    }

    #[Test]
    public function can_toggle_favorite_and_rename_a_file(): void
    {
        $this->patch(route('core.files.favorite', $this->file->id, false))
            ->assertStatus(200)
            ->assertJsonFragment(['isFavorite' => true]);

        $this->assertDatabaseHas('favorite_files', [
            'user_id' => $this->user->id,
            'file_id' => $this->file->id,
        ]);

        $this->patch(route('core.files.update', $this->file->id, false), ['name' => 'renamed-file'])
            ->assertStatus(200);

        $this->assertSame('renamed-file.png', $this->file->fresh()->original_name);
    }

    #[Test]
    public function can_generate_link_and_toggle_visibility(): void
    {
        $this->get(route('core.files.link', ['file' => $this->file->id, 'seconds' => 300], false))
            ->assertStatus(200)
            ->assertJsonStructure(['link']);

        $this->patch(route('core.files.makePublic', $this->file->id, false))
            ->assertStatus(200)
            ->assertJsonFragment(['isPublic' => true]);

        $this->patch(route('core.files.makePrivate', $this->file->id, false))
            ->assertStatus(200)
            ->assertJsonFragment(['isPublic' => false]);
    }

    #[Test]
    public function can_stream_and_download_a_file(): void
    {
        $this->get(route('core.files.show', $this->file->id, false))
            ->assertStatus(200);

        $this->get(route('core.files.download', $this->file->id, false))
            ->assertStatus(200);
    }

    private function createTestTable(): void
    {
        Schema::create('managed_attachable_models', function ($table) {
            $table->increments('id');
            $table->unsignedBigInteger('file_id')->nullable();
            $table->timestamps();
        });
    }
}

class ManagedAttachableModel extends Model implements Attachable, Extensions, MimeTypes
{
    protected $guarded = [];

    public function file(): Relation
    {
        return $this->belongsTo(File::class);
    }

    public function extensions(): array
    {
        return ['png'];
    }

    public function mimeTypes(): array
    {
        return ['image/png'];
    }
}
