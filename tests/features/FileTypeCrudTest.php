<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Files\Models\Type;
use LaravelEnso\Forms\TestTraits\DestroyForm;
use LaravelEnso\Forms\TestTraits\EditForm;
use LaravelEnso\Tables\Traits\Tests\Datatable;
use LaravelEnso\Users\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FileTypeCrudTest extends TestCase
{
    use Datatable, DestroyForm, EditForm, RefreshDatabase;

    private string $permissionGroup = 'administration.fileTypes';
    private Type $testModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs(User::first());

        $this->testModel = Type::factory()->make([
            'name' => 'Manual Uploads',
            'folder' => 'manualUploads',
            'model' => null,
            'icon' => 'folder',
            'description' => 'Manual files',
            'is_public' => false,
            'is_browsable' => false,
            'is_system' => false,
        ]);
    }

    #[Test]
    public function can_view_create_form(): void
    {
        $this->get(route($this->permissionGroup.'.create', [], false))
            ->assertStatus(200)
            ->assertJsonStructure(['form']);
    }

    #[Test]
    public function can_store_a_non_system_file_type(): void
    {
        $response = $this->post(route($this->permissionGroup.'.store', [], false), $this->testModel->toArray());

        $type = Type::query()->whereName($this->testModel->name)->first();

        $response->assertStatus(200)
            ->assertJsonFragment([
                'redirect' => 'administration.fileTypes.edit',
                'param' => ['type' => $type?->id],
            ]);

        $this->assertNotNull($type);
        $this->assertNull($type->model);
    }

    #[Test]
    public function rejects_invalid_model_when_type_is_system(): void
    {
        $this->post(route($this->permissionGroup.'.store', [], false), [
            ...$this->testModel->toArray(),
            'model' => 'App\\Models\\MissingModel',
            'is_system' => true,
        ])->assertStatus(302)
            ->assertSessionHasErrors(['model']);
    }

    #[Test]
    public function can_update_file_type(): void
    {
        $this->testModel->save();
        $this->testModel->description = 'Updated description';

        $this->patch(route($this->permissionGroup.'.update', $this->testModel->id, false), $this->testModel->toArray())
            ->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertSame('Updated description', $this->testModel->fresh()->description);
    }
}
