<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
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

    public function test_user_can_create_payment_method()
    {
        $this->user->givePermissionTo('payment-method-create');

        /** @var array $paymentMethod */
        $paymentMethod = PaymentMethod::factory()->make()->only('name');

        $response = $this->post(route('paymentMethods.store'), $paymentMethod);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payment_methods', $paymentMethod);
    }

    public function test_user_can_update_payment_method()
    {
        $this->user->givePermissionTo('payment-method-edit');

        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->put(route('paymentMethods.update', $paymentMethod->id), [
            'name' => 'test',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('payment_methods', [
            'id' => $paymentMethod->id,
            'name' => 'test',
            'updated_by' => $this->user->id,
        ]);
    }

    public function test_user_can_delete_payment_method()
    {
        $this->user->givePermissionTo('payment-method-delete');

        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->delete(route('paymentMethods.destroy', $paymentMethod->id));

        $response->assertStatus(204);

        $this->assertSoftDeleted('payment_methods', [
            'id' => $paymentMethod->id,
            'deleted_by' => $this->user->id,
        ]);
    }

    public function test_user_can_view_all_payment_methods()
    {
        $this->user->givePermissionTo('payment-method-list');

        PaymentMethod::factory(10)->create();

        $response = $this->get(route('paymentMethods.index'));

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');
    }

    public function test_user_can_view_one_payment_method()
    {
        $this->user->givePermissionTo('payment-method-list');

        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->get(route('paymentMethods.show', $paymentMethod->id));

        $response->assertStatus(200);

        $response->assertJson($paymentMethod->only('id', 'name'));
    }
}
