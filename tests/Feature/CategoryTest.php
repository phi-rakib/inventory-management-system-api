<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_can_create_category()
    {
        $this->user->givePermissionTo('category-create');

        $category = Category::factory()->make();

        $response = $this->post(route('categories.store'), $category->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('categories', [
            'name' => $category->name,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_user_can_update_category()
    {
        $this->user->givePermissionTo('category-edit');

        $category = Category::factory()->create();

        $response = $this->put(route('categories.update', $category), [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'updated_by' => $this->user->id,
        ]);
    }

    public function test_user_can_delete_category()
    {
        $this->user->givePermissionTo('category-delete');

        $category = Category::factory()->create();

        $response = $this->delete(route('categories.destroy', $category));

        $response->assertStatus(204);

        $this->assertSoftDeleted('categories', [
            'id' => $category->id,
            'deleted_by' => $this->user->id,
        ]);
    }

    public function test_user_can_view_all_categories()
    {
        $this->user->givePermissionTo('category-list');

        Category::factory(10)->create();

        $response = $this->get(route('categories.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'creator' => [
                        'id',
                        'name',
                    ],
                ],
            ],
        ]);
    }

    public function test_user_can_view_one_category()
    {
        $this->user->givePermissionTo('category-list');

        $category = Category::factory()->create();

        $response = $this->get(route('categories.show', $category));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
            'creator' => [
                'id',
                'name',
            ],
            'updater' => [
                'id',
                'name',
            ],
            'deleter' => [
                'id',
                'name',
            ],
            'products' => [
                '*' => [
                    'id',
                    'name',
                    'category_id',
                ],
            ],
        ]);
    }

    public function test_user_can_restore_category()
    {
        $this->user->givePermissionTo('category-restore');

        $category = Category::factory()->create();

        $category->delete();

        $this->assertSoftDeleted('categories', ['id' => $category->id]);

        $response = $this->get(route('categories.restore', $category->id));

        $response->assertOk();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_user_can_force_delete_category()
    {
        $this->user->givePermissionTo('category-force-delete');

        $category = Category::factory()->create();

        $response = $this->delete(route('categories.forceDelete', $category->id));

        $response->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
