<?php

namespace Tests\Feature;

use App\Models\Adjustment;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdjustmentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_can_create_adjustment()
    {
        $this->user->givePermissionTo('adjustment-create');

        $warehouse = Warehouse::factory()
            ->hasAttached(Product::factory()->count(2), [
                'quantity' => rand(10, 50),
            ])->create();

        $warehouseProducts = $warehouse->products->pluck('pivot');

        $adjustmentItems = $warehouseProducts->pluck('product_id')->map(function ($id) {
            return [
                'product_id' => $id,
                'quantity' => rand(1, 5),
                'type' => rand(0, 1) ? 'addition' : 'subtraction',
            ];
        });

        $data = [
            'warehouse_id' => $warehouse->id,
            'adjustment_date' => now(),
            'reason' => 'test reason',
            'adjustment_items' => $adjustmentItems->toArray(),
        ];

        $response = $this->post(route('adjustments.store'), $data);

        $response->assertStatus(201);

        $productsWithNewQuantity = $adjustmentItems->mapWithKeys(
            fn ($item) => [$item['product_id'] => ($item['type'] == 'addition' ? $item['quantity'] : $item['quantity'] * -1)]
        );

        foreach ($warehouseProducts as $warehouseProduct) {
            $this->assertDatabaseHas('product_warehouse', [
                'warehouse_id' => $warehouseProduct->warehouse_id,
                'product_id' => $warehouseProduct->product_id,
                'quantity' => $warehouseProduct->quantity + $productsWithNewQuantity[$warehouseProduct->product_id],
            ]);
        }

        $this->assertDatabaseHas('adjustments', [
            'warehouse_id' => $warehouse->id,
            'reason' => $data['reason'],
        ]);

        $adjustmentId = Adjustment::first()->id;

        foreach ($adjustmentItems as $item) {
            $this->assertDatabaseHas('adjustment_product', [
                'adjustment_id' => $adjustmentId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'type' => $item['type'],
            ]);
        }
    }

    public function test_user_can_read_all_adjustments()
    {
        $this->user->givePermissionTo('adjustment-list');

        Adjustment::factory(10)->create();

        $response = $this->get(route('adjustments.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'warehouse_id',
                    'adjustment_date',
                    'reason',
                    'products' => [
                        '*' => [
                            'id',
                            'name',
                            'pivot' => [
                                'adjustment_id',
                                'product_id',
                                'quantity',
                                'type',
                            ],
                        ],
                    ],
                    'creator' => [
                        'id',
                        'name',
                    ],
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $response->assertJsonCount(10, 'data');
    }

    public function test_user_can_read_an_adjustment()
    {
        $this->user->givePermissionTo('adjustment-list');

        $adjustment = Adjustment::factory()->create();

        $adjustmentItems = $adjustment->products;

        $response = $this->get(route('adjustments.show', $adjustment->id));

        $response->assertOk();

        $response->assertJsonStructure([
            'id',
            'warehouse_id',
            'adjustment_date',
            'reason',
            'products' => [
                '*' => [
                    'id',
                    'name',
                    'pivot' => [
                        'adjustment_id',
                        'product_id',
                        'quantity',
                        'type',
                    ],
                ],
            ],
            'creator' => [
                'id',
                'name',
            ],
            'created_at',
            'updated_at',
        ]);

        foreach ($adjustmentItems as $item) {
            $this->assertDatabaseHas('adjustment_product', [
                'adjustment_id' => $adjustment->id,
                'product_id' => $item->pivot->product_id,
                'quantity' => $item->pivot->quantity,
                'type' => $item->pivot->type,
            ]);
        }
    }

    public function test_user_can_update_an_adjustment()
    {
        $this->user->givePermissionTo('adjustment-edit');

        $adjustment = Adjustment::factory()->create();

        $adjustmentItems = $adjustment->products->pluck('pivot.product_id')->map(function ($id) {
            return [
                'product_id' => $id,
                'quantity' => rand(1, 10),
                'type' => rand(0, 1) ? 'addition' : 'subtraction',
            ];
        });

        $data = [
            'warehouse_id' => $adjustment->warehouse_id,
            'adjustment_date' => now(),
            'reason' => 'test reason',
            'adjustment_items' => $adjustmentItems->toArray(),
        ];

        $response = $this->put(route('adjustments.update', $adjustment), $data);

        $response->assertOk();

        $this->assertDatabaseHas('adjustments', [
            'id' => $adjustment->id,
            'warehouse_id' => $data['warehouse_id'],
            'reason' => $data['reason'],
        ]);

        foreach ($adjustmentItems as $item) {
            $this->assertDatabaseHas('adjustment_product', [
                'adjustment_id' => $adjustment->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'type' => $item['type'],
            ]);
        }
    }

    public function test_user_can_delete_an_adjustment()
    {
        $this->user->givePermissionTo('adjustment-delete');

        $adjustment = Adjustment::factory()->create();

        $response = $this->delete(route('adjustments.destroy', $adjustment));

        $response->assertNoContent();

        $this->assertDatabaseCount('adjustments', 0);

        $this->assertDatabaseCount('adjustment_product', 0);
    }
}
