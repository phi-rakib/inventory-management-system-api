<?php

namespace Tests\Feature;

use App\Models\Account;
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

    public function test_user_can_create_deposit()
    {
        $this->user->givePermissionTo('deposit-create');

        $deposit = Deposit::factory()->make();

        $account = $deposit->account;

        $response = $this->post(route('deposits.store'), $deposit->toArray());

        $response->assertCreated();

        $this->assertDatabaseHas('deposits', [
            'account_id' => $deposit->account_id,
            'amount' => $deposit->amount,
            'deposit_date' => $deposit->deposit_date,
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $deposit->account_id,
            'balance' => $account->balance + $deposit->amount,
        ]);
    }

    public function test_user_can_update_deposit()
    {
        $this->user->givePermissionTo(['deposit-edit', 'deposit-create']);

        $account = Account::factory()->create(['balance' => 1000]);
        $deposit = Deposit::factory()->make([
            'account_id' => $account->id,
            'amount' => 100
        ]);

        $this->post(route('deposits.store'), $deposit->toArray());

        $depositId = Deposit::first()->id;

        $updatedAmount = 150;
        $updatedData = [
            ...$deposit->toArray(),
            'amount' => $updatedAmount,
        ];
        
        $response = $this->put(route('deposits.update', $depositId), $updatedData);

        $response->assertOk();
        $response->assertJson(['message' => 'Deposit updated']);

        $this->assertDatabaseHas('deposits', [
            'id' => $depositId,
            'amount' => $updatedAmount,
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => $account->balance + $updatedAmount,
        ]);
    }

    public function test_insufficient_balance_when_update_deposit()
    {
        $this->user->givePermissionTo('deposit-edit');

        $deposit = Deposit::factory()->create();

        $account = $deposit->account;

        $deposit->account()->decrement('balance', $account->balance);

        $response = $this->put(route('deposits.update', $deposit->id), [
            ...$deposit->toArray(),
            'amount' => 50,
        ]);

        $response->assertBadRequest();

        $response->assertJson(['error' => 'Account Balance is less than the deposited amount']);

        $this->assertDatabaseHas('deposits', [
            'id' => $deposit->id,
            'amount' => $deposit->amount,
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => 0,
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

    public function test_account_insufficient_balance_in_deposit_force_delete()
    {
        $this->user->givePermissionTo('deposit-force-delete');

        $deposit = Deposit::factory()->create();

        $deposit->account()->decrement('balance', 10);

        $response = $this->withExceptionHandling()->delete(route('deposits.forceDelete', $deposit->id));

        $response->assertBadRequest();

        $response->assertJson(['error' => 'Account Balance is less than the deposited amount']);

        $this->assertDatabaseHas('deposits', ['id' => $deposit->id]);
    }

    public function test_account_insufficient_balance_in_deposit_destroy()
    {
        $this->user->givePermissionTo('deposit-delete');

        $deposit = Deposit::factory()->create();

        $deposit->account()->decrement('balance', 10);

        $response = $this->withExceptionHandling()->delete(route('deposits.destroy', $deposit->id));

        $response->assertBadRequest();

        $response->assertJson(['error' => 'Account Balance is less than the deposited amount']);

        $this->assertNotSoftDeleted('deposits', ['id' => $deposit->id]);
    }
}
