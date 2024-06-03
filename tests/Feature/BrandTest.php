<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_can_create_brand()
    {
        $this->user->givePermissionTo('brand-create');

        $brand = Brand::factory()->make();

        $response = $this->post(route('brands.store'), $brand->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('brands', [
            'name' => $brand->name,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_user_can_update_brand()
    {
        $this->user->givePermissionTo('brand-edit');

        $brand = Brand::factory()->create();

        $response = $this->put(route('brands.update', $brand), [
            'name' => 'Updated brand',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Updated brand',
            'updated_by' => $this->user->id,
        ]);
    }

    public function test_user_can_delete_brand()
    {
        $this->user->givePermissionTo('brand-delete');

        $brand = Brand::factory()->create();

        $response = $this->delete(route('brands.destroy', $brand));

        $response->assertStatus(204);

        $this->assertSoftDeleted('brands', [
            'id' => $brand->id,
            'deleted_by' => $this->user->id,
        ]);
    }

    public function test_user_can_read_all_brands()
    {
        $this->user->givePermissionTo('brand-list');

        Brand::factory(10)->create();

        $response = $this->get(route('brands.index'));

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

    public function test_user_can_read_brand()
    {
        $this->user->givePermissionTo('brand-list');

        $brand = Brand::factory()->create();

        $response = $this->get(route('brands.show', $brand));

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $brand->id,
            'name' => $brand->name,
        ]);

        $response->assertJsonStructure([
            'id',
            'name',
            'creator' => [
                'id',
                'name',
            ],
        ]);
    }
}
