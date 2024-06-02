<?php

namespace Tests\Feature;

use App\Models\Deposit;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DepositTest extends TestCase
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

        $response = $this->delete(route('deposits.destroy', $deposit));

        $response->assertStatus(204);

        $this->assertSoftDeleted('deposits', [
            'id' => $deposit->id,
            'deleted_by' => $this->user->id,
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
}
