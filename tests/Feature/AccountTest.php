<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountTest extends TestCase
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

    public function test_user_can_create_an_account()
    {
        $this->user->givePermissionTo('account-create');

        $account = Account::factory()->make();

        $response = $this->post(route('accounts.store', $account->toArray()));

        $response->assertStatus(201);

        $this->assertDatabaseHas('accounts', [
            'name' => $account->name,
            'account_number' => $account->account_number,
            'balance' => $account->balance,
            'created_by' => $this->user->id,
        ]);
    }

    public function test_user_can_update_an_account()
    {
        $this->user->givePermissionTo('account-edit');

        $account = Account::factory()->create();

        $response = $this->put(route('accounts.update', $account->id), [
            'name' => 'updated name',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'updated name',
        ]);
    }

    public function test_user_can_delete_an_account()
    {
        $this->user->givePermissionTo('account-delete');

        $account = Account::factory()->create();

        $response = $this->delete(route('accounts.destroy', $account->id));

        $response->assertStatus(204);

        $this->assertSoftDeleted('accounts', [
            'id' => $account->id,
        ]);
    }

    public function test_user_can_read_an_account()
    {
        $this->user->givePermissionTo('account-list');

        $account = Account::factory()->create();

        $response = $this->get(route('accounts.show', $account->id));

        $response->assertStatus(200);

        $response->assertJson([
            'id' => $account->id,
            'name' => $account->name,
        ]);
    }

    public function test_user_can_read_all_accounts()
    {
        $this->user->givePermissionTo('account-list');

        Account::factory(10)->create();

        $response = $this->get(route('accounts.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');
    }
}
