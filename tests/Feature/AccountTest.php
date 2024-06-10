<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
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
            'deleted_by' => $this->user->id,
        ]);
    }

    public function test_user_can_read_an_account()
    {
        $this->user->givePermissionTo('account-list');

        $account = Account::factory()->create();

        $response = $this->get(route('accounts.show', $account->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
            'account_number',
            'balance',
            'creator' => [
                'id',
                'name',
            ],
            'updater' => [
                'id',
                'name',
            ],
            'deleter' => [
                'id',
                'name',
            ],
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

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

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'account_number',
                    'balance',
                    'creator' => [
                        'id',
                        'name',
                    ],
                    'updater' => [
                        'id',
                        'name',
                    ],
                    'deleter' => [
                        'id',
                        'name',
                    ],
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);
    }
}
