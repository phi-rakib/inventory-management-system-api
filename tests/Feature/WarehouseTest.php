<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class WarehouseTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_can_create_warehouse()
    {
        $this->user->givePermissionTo('warehouse-create');

        $warehouse = Warehouse::factory()->make();

        $response = $this->post(route('warehouses.store'), $warehouse->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('warehouses', [
            'name' => $warehouse->name,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_user_can_update_warehouse()
    {
        $this->user->givePermissionTo('warehouse-edit');

        $warehouse = Warehouse::factory()->create();

        $response = $this->put(route('warehouses.update', $warehouse), [
            ...$warehouse->toArray(),
            'name' => 'Updated Warehouse',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('warehouses', [
            'name' => 'Updated Warehouse',
            'updated_by' => $this->user->id,
        ]);
    }

    public function test_user_can_delete_warehouse()
    {
        $this->user->givePermissionTo('warehouse-delete');

        $warehouse = Warehouse::factory()->create();

        $response = $this->delete(route('warehouses.destroy', $warehouse));

        $response->assertStatus(204);

        $this->assertSoftDeleted('warehouses', [
            'id' => $warehouse->id,
            'deleted_by' => $this->user->id,
        ]);
    }

    public function test_user_can_view_all_warehouses()
    {
        $this->user->givePermissionTo('warehouse-list');

        Warehouse::factory(10)->create();

        $response = $this->get(route('warehouses.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'created_by',
                    'created_at',
                    'updated_by',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_user_can_view_warehouse()
    {
        $this->user->givePermissionTo('warehouse-list');

        $warehouse = Warehouse::factory()->create();

        $response = $this->get(route('warehouses.show', $warehouse));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
            'created_by',
            'created_at',
            'updated_by',
            'updated_at',
        ]);
    }

    public function test_user_can_restore_warehouse()
    {
        $this->user->givePermissionTo('warehouse-restore');

        $warehouse = Warehouse::factory()->create();

        $warehouse->delete();

        $this->assertSoftDeleted('warehouses', ['id' => $warehouse->id]);

        $response = $this->get(route('warehouses.restore', $warehouse->id));

        $response->assertOk();

        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouse->id,
            'deleted_at' => null,
        ]);
    }

    public function test_user_can_force_delete_warehouse()
    {
        $this->user->givePermissionTo('warehouse-force-delete');

        $warehouse = Warehouse::factory()->create();

        $response = $this->delete(route('warehouses.forceDelete', $warehouse->id));

        $response->assertNoContent();

        $this->assertDatabaseMissing('warehouses', ['id' => $warehouse->id]);
    }
}
