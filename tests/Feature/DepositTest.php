<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Exceptions;
use Tests\TestCase;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
    }

    public function test_user_can_deposit()
    {
        $this->user->givePermissionTo('deposit-create');

        $deposit = Deposit::factory()->make();

        $response = $this->post(route('deposits.store'), $deposit->toArray());

        $response->assertStatus(201);

        $response->assertJson([
            'message' => "$deposit->amount Deposited in account $deposit->account->name",
        ]);

        $this->assertDatabaseHas('deposits', [
            'account_id' => $deposit->account_id,
            'amount' => $deposit->amount,
            'deposit_date' => $deposit->deposit_date,
        ]);
    }

    public function test_user_can_update_deposit()
    {
        $this->user->givePermissionTo('deposit-edit');

        $deposit = Deposit::factory()->create();

        $response = $this->put(route('deposits.update', $deposit), [
            'amount' => 5000,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'amount' => 5000,
        ]);
    }

    public function test_user_can_delete_deposit()
    {
        $this->user->givePermissionTo('deposit-delete');

        $deposit = Deposit::factory()->create();

        $account = $deposit->account;

        $this->assertEquals($deposit->amount, $account->balance);

        $response = $this->delete(route('deposits.destroy', $deposit));

        $response->assertStatus(204);

        $this->assertSoftDeleted('deposits', [
            'id' => $deposit->id,
            'deleted_by' => $this->user->id,
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => 0,
        ]);
    }

    public function test_user_can_view_all_deposits()
    {
        $this->user->givePermissionTo('deposit-list');

        Deposit::factory(10)->create();

        $response = $this->get(route('deposits.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');
    }

    public function test_user_can_view_deposit()
    {
        $this->user->givePermissionTo('deposit-list');

        $deposit = Deposit::factory()->create();

        $response = $this->get(route('deposits.show', $deposit->id));

        $response->assertStatus(200);

        $response->assertJson($deposit->toArray());
    }

    public function test_user_can_restore_deposit()
    {
        $this->user->givePermissionTo(['deposit-delete', 'deposit-restore']);

        $deposit = Deposit::factory()->create();

        $this->delete(route('deposits.destroy', $deposit->id));

        $response = $this->get(route('deposits.restore', $deposit->id));

        $response->assertOk();

        $this->assertDatabaseHas('deposits', ['id' => $deposit->id]);

        $account = $deposit->account;
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => $deposit->amount,
        ]);
    }

    public function test_user_can_force_delete_deposit()
    {
        $this->user->givePermissionTo('deposit-force-delete');

        $deposit = Deposit::factory()->create();

        $account = $deposit->account;

        $response = $this->delete(route('deposits.forceDelete', $deposit->id));

        $response->assertNoContent();

        $this->assertDatabaseMissing('deposits', ['id' => $deposit->id]);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => 0,
        ]);
    }

    public function test_exception_when_account_balance_is_less_than_deposited_amount_in_deposit_force_delete()
    {
        Exceptions::fake();

        $this->user->givePermissionTo('deposit-force-delete');

        $deposit = Deposit::factory()->create();

        $deposit->account()->decrement('balance', 10);

        $this->withExceptionHandling()->delete(route('deposits.forceDelete', $deposit->id));

        Exceptions::assertReported(function (Exception $e) {
            return $e->getMessage() == 'Account Balance is less than deposited amount';
        });
    }

    public function test_exception_when_account_balance_is_less_than_deposited_amount_in_deposit_destroy()
    {
        Exceptions::fake();

        $this->user->givePermissionTo('deposit-delete');

        $deposit = Deposit::factory()->create();

        $deposit->account()->decrement('balance', 10);

        $this->withExceptionHandling()->delete(route('deposits.destroy', $deposit->id));

        Exceptions::assertReported(function (Exception $ex) {
            return $ex->getMessage() == 'Account Balance is less than deposited amount';
        });
    }
}
