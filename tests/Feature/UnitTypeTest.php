<?php

namespace Tests\Feature;

use App\Models\UnitType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UnitTypeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_can_create_unit_type()
    {
        $this->user->givePermissionTo('unit-type-create');

        $unitType = UnitType::factory()->make();

        $response = $this->post(route('unitTypes.store'), $unitType->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('unit_types', [
            'name' => $unitType->name,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_user_can_update_unit_type()
    {
        $this->user->givePermissionTo('unit-type-edit');

        $unitType = UnitType::factory()->create();

        $response = $this->put(route('unitTypes.update', $unitType), [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('unit_types', [
            'name' => 'Updated Name',
            'updated_by' => $this->user->id,
        ]);
    }

    public function test_user_can_delete_unit_type()
    {
        $this->user->givePermissionTo('unit-type-delete');

        $unitType = UnitType::factory()->create();

        $response = $this->delete(route('unitTypes.destroy', $unitType));

        $response->assertStatus(204);

        $this->assertSoftDeleted('unit_types', [
            'id' => $unitType->id,
            'deleted_by' => $this->user->id,
        ]);
    }

    public function test_user_can_read_all_unit_types()
    {
        $this->user->givePermissionTo('unit-type-list');

        UnitType::factory(10)->create();

        $response = $this->get(route('unitTypes.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'created_at',
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
                ],
            ],
        ]);
    }

    public function test_user_can_read_unit_type()
    {
        $this->user->givePermissionTo('unit-type-list');

        $unitType = UnitType::factory()->create();

        $response = $this->get(route('unitTypes.show', $unitType));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
            'description',
            'created_at',
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
        ]);
    }
}
