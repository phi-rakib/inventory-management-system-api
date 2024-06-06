<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_view_all_suppliers()
    {
        $this->user->givePermissionTo('supplier-list');

        $response = $this->get(route('suppliers.index'));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'phone',
                    'email',
                    'creator' => [
                        'id',
                        'name',
                    ],
                ],
            ],
        ]);
    }

    public function test_user_can_view_one_supplier()
    {
        $this->user->givePermissionTo('supplier-list');

        $supplier = Supplier::factory()->create();

        $response = $this->get(route('suppliers.show', $supplier));

        $response->assertOk();

        $response->assertJsonStructure([
            'id',
            'name',
            'phone',
            'email',
            'creator' => [
                'id',
                'name',
            ],
            'account' => [
                'id',
                'name',
                'status',
                'balance',
            ],
        ]);
    }

    public function test_user_can_create_supplier()
    {
        $this->user->givePermissionTo('supplier-create');

        $supplier = Supplier::factory()->state([
            'password' => fake()->password(),
            'account_id' => null,
            'user_id' => null,
        ])->make();

        $response = $this->post(route('suppliers.store'), $supplier->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('suppliers', [
            'name' => $supplier->name,
            'phone' => $supplier->phone,
        ]);

        $supplier = Supplier::with('user')->where('email', $supplier->email)->first();

        $this->assertDatabaseHas('accounts', [
            'name' => $supplier->name,
            'user_id' => $supplier->user_id,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $supplier->email,
            'id' => $supplier->user_id,
        ]);

        $roles = $supplier->user->getRoleNames();
        $this->assertContains('Supplier', $roles);
    }

    public function test_user_can_update_supplier()
    {
        $this->user->givePermissionTo('supplier-edit');

        $supplier = Supplier::factory()->create();

        $response = $this->put(route('suppliers.update', $supplier), [
            'name' => 'Updated Name',
            'account_number' => $supplier->account->account_number,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Updated Name',
            'account_id' => $supplier->account_id,
        ]);

        $this->assertDatabaseHas('accounts', [
            'account_number' => $supplier->account->account_number,
            'id' => $supplier->account_id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $supplier->user_id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_user_can_delete_supplier()
    {
        $this->user->givePermissionTo('supplier-delete');

        $supplier = Supplier::factory()->create();

        $response = $this->delete(route('suppliers.destroy', $supplier));

        $response->assertNoContent();

        $this->assertSoftDeleted('suppliers', [
            'id' => $supplier->id,
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $supplier->account_id,
            'status' => 'inactive',
        ]);
    }
}
