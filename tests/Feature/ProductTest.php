<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_can_view_all_products()
    {
        $this->user->givePermissionTo('product-list');

        Warehouse::factory()->has(Product::factory()->count(10)->hasAttributes(3)->hasPrices(1))->create();

        $response = $this->get(route('products.index'));

        $response->assertOk();

        $response->assertJsonCount(10, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'category' => [
                        'id',
                        'name',
                    ],
                    'brand' => [
                        'id',
                        'name',
                    ],
                    'unit_type' => [
                        'id',
                        'name',
                    ],
                    'creator' => [
                        'id',
                        'name',
                    ],
                    'warehouses' => [
                        '*' => [
                            'id',
                            'name',
                            'pivot' => [
                                'quantity',
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_user_can_view_product()
    {
        $this->user->givePermissionTo('product-list');

        $product = Product::factory()->hasAttributes(10)->hasPrices(1)->create();

        $response = $this->get(route('products.show', $product));

        $response->assertOk();

        $response->assertJsonStructure([
            'id',
            'name',
            'category' => [
                'id',
                'name',
            ],
            'brand' => [
                'id',
                'name',
            ],
            'unit_type' => [
                'id',
                'name',
            ],
            'creator' => [
                'id',
                'name',
            ],
            'attributes' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
            'prices' => [
                '*' => [
                    'price',
                ],
            ],
            'warehouses' => [
                '*' => [
                    'id',
                    'name',
                    'pivot' => [
                        'quantity',
                    ],
                ],
            ],
        ]);

    }

    public function test_user_can_create_a_product()
    {
        $this->user->givePermissionTo('product-create');

        $attributes = Attribute::factory(10)->create();
        $product = Product::factory()->make();
        $randomAttributes = $attributes->random(3)->pluck('id')->toArray();

        $data = [
            'price' => $price = rand(100, 1000),
            ...$product->toArray(),
            'attributes' => $randomAttributes,
        ];

        $this->post(route('products.store'), $data)
            ->assertCreated();

        $this->assertDatabaseHas('products', [
            'name' => $product->name,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'unit_type_id' => $product->unit_type_id,
            'created_by' => $this->user->id,
        ]);

        $productId = Product::first()->id;

        $this->assertDatabaseHas('prices', [
            'price' => $price,
            'product_id' => $productId,
        ]);

        $this->assertDatabaseCount('attribute_product', count($randomAttributes));

        foreach ($randomAttributes as $attribute) {
            $this->assertDatabaseHas('attribute_product', [
                'product_id' => $productId,
                'attribute_id' => $attribute,
            ]);
        }
    }

    public function test_user_can_update_a_product()
    {
        $this->user->givePermissionTo('product-edit');

        $product = Product::factory()->hasAttributes(10)->hasPrices(1)->create();

        $newRandomAttributes = $product->attributes->random(5)->pluck('id')->toArray();

        $data = [
            'price' => $newPrice = rand(100, 1000),
            'name' => 'updated name',
            'attributes' => $newRandomAttributes,
            'category_id' => $product->category_id,
            'brand_id' => $product->brand_id,
            'unit_type_id' => $product->unit_type_id,
        ];

        $response = $this->put(route('products.update', $product->id), $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'updated name',
            'updated_by' => $this->user->id,
        ]);

        $this->assertDatabaseHas('prices', [
            'price' => $newPrice,
            'product_id' => $product->id,
        ]);

        $this->assertDatabaseCount('attribute_product', count($newRandomAttributes));

        foreach ($newRandomAttributes as $attribute) {
            $this->assertDatabaseHas('attribute_product', [
                'product_id' => $product->id,
                'attribute_id' => $attribute,
            ]);
        }
    }

    public function test_user_can_delete_a_product()
    {
        $this->user->givePermissionTo('product-delete');

        $product = Product::factory()->hasAttributes(10)->hasPrices(1)->create();

        $this->delete(route('products.destroy', $product->id))
            ->assertNoContent();

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
            'deleted_by' => $this->user->id,
        ]);

        $productAttributes = $product->attributes->pluck('pivot');

        $this->assertDatabaseHas('prices', [
            'price' => $product->latestPrice()->pluck('price'),
            'product_id' => $product->id,
        ]);

        $this->assertDatabaseCount('attribute_product', $productAttributes->count());

        foreach ($productAttributes as $attribute) {
            $this->assertDatabaseHas('attribute_product', [
                'product_id' => $product->id,
                'attribute_id' => $attribute->attribute_id,
            ]);
        }
    }
}
