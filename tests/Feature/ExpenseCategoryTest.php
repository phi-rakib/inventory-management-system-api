<?php

namespace Tests\Feature;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ExpenseCategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_can_create_expense_category()
    {
        $this->user->givePermissionTo('expense-category-create');

        $expenseCategory = ExpenseCategory::factory()->make();

        $response = $this->post(route('expenseCategories.store'), $expenseCategory->toArray());

        $response->assertStatus(201);

        $this->assertDatabaseHas('expense_categories', [
            'name' => $expenseCategory->name,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_user_can_update_expense_category()
    {
        $this->user->givePermissionTo('expense-category-edit');

        $expenseCategory = ExpenseCategory::factory()->create();

        $response = $this->put(route('expenseCategories.update', $expenseCategory->id), [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('expense_categories', [
            'id' => $expenseCategory->id,
            'name' => 'Updated Name',
            'updated_by' => $this->user->id,
        ]);
    }

    public function test_user_can_delete_expense_category()
    {
        $this->user->givePermissionTo('expense-category-delete');

        $expenseCategory = ExpenseCategory::factory()->create();

        $response = $this->delete(route('expenseCategories.destroy', $expenseCategory->id));

        $response->assertStatus(204);

        $this->assertSoftDeleted('expense_categories', [
            'id' => $expenseCategory->id,
            'deleted_by' => $this->user->id,
        ]);
    }

    public function test_user_can_read_all_expense_categories()
    {
        $this->user->givePermissionTo('expense-category-list');

        ExpenseCategory::factory(10)->create();

        $response = $this->get(route('expenseCategories.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');
    }

    public function test_user_can_read_expense_category()
    {
        $this->user->givePermissionTo('expense-category-list');

        $expenseCategory = ExpenseCategory::factory()->create();

        $response = $this->get(route('expenseCategories.show', $expenseCategory->id));

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $expenseCategory->id,
            'name' => $expenseCategory->name,
        ]);
    }

    public function test_user_can_restore_expense_category()
    {
        $this->user->givePermissionTo(['expense-category-restore', 'expense-category-delete']);

        $expenseCategory = ExpenseCategory::factory()->create();

        $this->delete(route('expenseCategories.destroy', $expenseCategory->id));

        $response = $this->get(route('expenseCategories.restore', $expenseCategory->id));

        $response->assertOk();

        $this->assertDatabasehas('expense_categories', ['id' => $expenseCategory->id, 'deleted_at' => null]);
    }

    public function test_user_can_force_delete_category()
    {
        $this->user->givePermissionTo('expense-category-force-delete');

        $expenseCategory = ExpenseCategory::factory()->create();

        $response = $this->delete(route('expenseCategories.forceDelete', $expenseCategory->id));

        $response->assertNoContent();

        $this->assertDatabaseMissing('expense_categories', ['id' => $expenseCategory->id]);
    }
}
