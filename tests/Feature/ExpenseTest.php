<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);

        $this->artisan('db:seed', ['--class' => PermissionSeeder::class]);
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_user_can_create_expense()
    {
        $this->user->givePermissionTo('expense-create');

        $expense = Expense::factory()->make();

        $response = $this->post(route('expenses.store'), $expense->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('expenses', $expense->toArray());
    }

    public function test_user_can_update_expense()
    {
        $this->user->givePermissionTo('expense-edit');

        $expense = Expense::factory()->create();

        $response = $this->put(route('expenses.update', $expense->id), [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'name' => 'Updated Name',
            'updated_by' => auth()->id(),
        ]);
    }

    public function test_user_can_delete_expense()
    {
        $this->user->givePermissionTo('expense-delete');

        $expense = Expense::factory()->create();

        $response = $this->delete(route('expenses.destroy', $expense->id));

        $response->assertStatus(204);

        $this->assertSoftDeleted('expenses', [
            'id' => $expense->id,
            'deleted_by' => auth()->id(),
        ]);
    }

    public function test_user_can_read_all_expenses()
    {
        $this->user->givePermissionTo('expense-list');

        Expense::factory(10)->create();

        $response = $this->get(route('expenses.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'amount',
                    'account_id',
                    'expense_category_id',
                    'payment_method_id',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'account' => [
                        'id',
                        'name',
                    ],
                    'expense_category' => [
                        'id',
                        'name',
                    ],
                    'payment_method' => [
                        'id',
                        'name',
                    ],
                    'creator' => [
                        'id',
                        'name',
                    ],
                ],
            ],
        ]);
    }

    public function test_user_can_read_one_expense()
    {
        $this->user->givePermissionTo('expense-list');

        $expense = Expense::factory()->create();

        $response = $this->get(route('expenses.show', $expense->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
            'amount',
            'account_id',
            'expense_category_id',
            'payment_method_id',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'account' => [
                'id',
                'name',
            ],
            'expense_category' => [
                'id',
                'name',
            ],
            'payment_method' => [
                'id',
                'name',
            ],
            'creator' => [
                'id',
                'name',
            ],
        ]);
    }
}