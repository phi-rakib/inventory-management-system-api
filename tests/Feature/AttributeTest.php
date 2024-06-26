<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AttributeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_can_create_attribute()
    {
        $this->user->givePermissionTo('attribute-create');

        $attribute = Attribute::factory()->make();

        $response = $this->post(route('attributes.store'), $attribute->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('attributes', $attribute->toArray());
    }

    public function test_user_can_update_attribute()
    {
        $this->user->givePermissionTo('attribute-edit');

        $attribute = Attribute::factory()->create();

        $response = $this->put(route('attributes.update', $attribute), [
            'name' => 'updated name',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('attributes', [
            'id' => $attribute->id,
            'name' => 'updated name',
            'updated_by' => $this->user->id,
        ]);
    }

    public function test_user_can_delete_attribute()
    {
        $this->user->givePermissionTo('attribute-delete');

        $attribute = Attribute::factory()
            ->hasAttributeValues(2)
            ->create();

        $response = $this->delete(route('attributes.destroy', $attribute));

        $response->assertStatus(204);

        $this->assertSoftDeleted('attributes', [
            'id' => $attribute->id,
            'deleted_by' => $this->user->id,
        ]);

        foreach ($attribute->attributeValues as $attributeValue) {
            $this->assertSoftDeleted('attribute_values', [
                'id' => $attributeValue->id,
            ]);
        }
    }

    public function test_user_can_view_all_attributes()
    {
        $this->user->givePermissionTo('attribute-list');

        Attribute::factory(10)->create();

        $response = $this->get(route('attributes.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
        ]);
    }

    public function test_user_can_view_attribute()
    {
        $this->user->givePermissionTo('attribute-list');

        $attribute = Attribute::factory()->create();

        $response = $this->get(route('attributes.show', $attribute));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
        ]);
    }

    public function test_user_can_restore_attribute()
    {
        $this->user->givePermissionTo(['attribute-restore', 'attribute-delete']);

        $attribute = Attribute::factory()->hasAttributeValues(2)->create();

        $attributeValues = $attribute->attributeValues;

        $this->delete(route('attributes.destroy', $attribute->id));

        $response = $this->get(route('attributes.restore', $attribute->id));

        $response->assertOk();

        $this->assertDatabaseHas('attributes', [
            'id' => $attribute->id,
        ]);

        foreach ($attributeValues as $attributeValue) {
            $this->assertDatabaseHas('attribute_values', [
                'id' => $attributeValue->id,
                'attribute_id' => $attribute->id,
            ]);
        }
    }

    public function test_user_can_force_delete_attribute()
    {
        $this->user->givePermissionTo('attribute-force-delete');

        $attribute = Attribute::factory()->hasAttributeValues(2)->create();

        $attributeValues = $attribute->attributeValues;

        $response = $this->delete(route('attributes.forceDelete', $attribute->id));

        $response->assertNoContent();

        $this->assertDatabaseMissing('attributes', ['id' => $attribute->id]);

        foreach ($attributeValues as $attributeValue) {
            $this->assertDatabaseMissing('attribute_values', [
                'id' => $attributeValue->id,
                'attribute_id' => $attribute->id,
            ]);
        }
    }
}
