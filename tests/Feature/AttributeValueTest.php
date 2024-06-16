<?php

namespace Tests\Feature;

use App\Models\AttributeValue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AttributeValueTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_can_create_attribute_value()
    {
        $this->user->givePermissionTo('attribute-value-create');

        $attributeValue = AttributeValue::factory()->make();

        $response = $this->post(route('attributeValues.store'), $attributeValue->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('attribute_values', [
            'name' => $attributeValue->name,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_user_can_update_attribute_value()
    {
        $this->user->givePermissionTo('attribute-value-edit');

        $attributeValue = AttributeValue::factory()->create();

        $response = $this->put(route('attributeValues.update', $attributeValue->id), [
            'name' => 'test',
            'attribute_id' => $attributeValue->attribute_id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('attribute_values', [
            'name' => 'test',
            'updated_by' => $this->user->id,
        ]);
    }

    public function test_user_can_delete_attribute_value()
    {
        $this->user->givePermissionTo('attribute-value-delete');

        $attributeValue = AttributeValue::factory()->create();

        $response = $this->delete(route('attributeValues.destroy', $attributeValue->id));

        $response->assertStatus(204);

        $this->assertSoftDeleted('attribute_values', [
            'id' => $attributeValue->id,
            'deleted_by' => $this->user->id,
        ]);
    }

    public function test_user_can_read_all_attribute_values()
    {
        $this->user->givePermissionTo('attribute-value-list');

        AttributeValue::factory(10)->create();

        $response = $this->get(route('attributeValues.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                    'attribute' => [
                        'id',
                        'name',
                    ],
                ],
            ],
        ]);
    }

    public function test_user_can_view_one_attribute_value()
    {
        $this->user->givePermissionTo('attribute-value-list');

        $attributeValue = AttributeValue::factory()->create();

        $response = $this->get(route('attributeValues.show', $attributeValue->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
            'created_at',
            'updated_at',
            'attribute' => [
                'id',
                'name',
            ],
        ]);
    }

    public function test_user_can_restore_attribute_value()
    {
        $this->user->givePermissionTo(['attribute-value-restore', 'attribute-value-delete']);

        $attributeValue = AttributeValue::factory()->create();

        $this->delete(route('attributeValues.destroy', $attributeValue->id));

        $response = $this->get(route('attributeValues.restore', $attributeValue->id));

        $response->assertOk();

        $this->assertDatabaseHas('attribute_values', ['id' => $attributeValue->id]);
    }

    public function test_user_can_force_delete_attribute_value()
    {
        $this->user->givePermissionTo('attribute-value-force-delete');

        $attributeValue = AttributeValue::factory()->create();

        $response = $this->delete(route('attributeValues.forceDelete', $attributeValue->id));

        $response->assertNoContent();

        $this->assertDatabaseMissing('attribute_values', ['id' => $attributeValue->id]);
    }
}
