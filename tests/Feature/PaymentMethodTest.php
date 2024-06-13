<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Auth::user();
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

    public function test_user_can_restore_payment_method()
    {
        $this->user->givePermissionTo('payment-method-restore');

        $paymentMethod = PaymentMethod::factory()->create();

        $paymentMethod->delete();

        $this->assertSoftDeleted('payment_methods', [
            'id' => $paymentMethod->id,
        ]);

        $response = $this->get(route('paymentMethods.restore', $paymentMethod->id));

        $response->assertOk();

        $this->assertDatabaseHas('payment_methods', [
            'id' => $paymentMethod->id,
        ]);
    }
}
