<?php

namespace Tests\Feature;

use App\Models\DepositCategory;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepositCategoryTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        Sanctum::actingAs($this->user, ['*']);

        $this->artisan('db:seed', ['--class' => PermissionSeeder::class]);
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_user_can_create_deposit_category(): void
    {
        $this->user->givePermissionTo('deposit-category-create');

        /** @var array $depositCategory * */
        $depositCategory = DepositCategory::factory()->make()->only(['name', 'description']);

        $response = $this->post(route('depositCategories.store'), $depositCategory);

        $response->assertStatus(201);

        $this->assertDatabaseHas('deposit_categories', $depositCategory);
    }

    public function test_user_can_update_deposit_category(): void
    {
        $this->user->givePermissionTo('deposit-category-edit');

        $depositCategory = DepositCategory::factory()->create();

        $depositCategory->name = 'Updated name';

        $response = $this->put(route('depositCategories.update', $depositCategory), $depositCategory->toArray());

        $response->assertStatus(200);

        $this->assertDatabaseHas('deposit_categories', [
            'id' => $depositCategory->id,
            'name' => 'Updated name',
            'updated_by' => auth()->id(),
        ]);
    }

    public function test_user_can_read_deposit_category(): void
    {
        $this->user->givePermissionTo('deposit-category-list');

        $depositCategory = DepositCategory::factory()->create();

        $response = $this->get(route('depositCategories.show', $depositCategory->id));

        $response->assertStatus(200);

        $response->assertJson($depositCategory->toArray());
    }

    public function test_user_can_delete_deposit_category(): void
    {
        $this->user->givePermissionTo('deposit-category-delete');

        $depositCategory = DepositCategory::factory()->create();

        $response = $this->delete(route('depositCategories.destroy', $depositCategory->id));

        $response->assertStatus(204);

        $this->assertSoftDeleted('deposit_categories', [
            'id' => $depositCategory->id,
            'deleted_by' => auth()->id(),
        ]);
    }

    public function test_user_can_read_all_deposit_categories(): void
    {
        $this->user->givePermissionTo('deposit-category-list');

        DepositCategory::factory(10)->create();

        $response = $this->get(route('depositCategories.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');
    }
}
