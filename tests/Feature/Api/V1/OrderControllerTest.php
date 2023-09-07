<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Base;

use function PHPUnit\Framework\assertJsonStringNotEqualsJsonString;

class OrderControllerTest extends Base
{
    use RefreshDatabase;
    use WithFaker;

    public Order $userOrder;
    public Order $customOrder;
    public User $customUser;
    public OrderStatus $orderStatus;
    public Product $product;
    private mixed $payment;

    public function defaultFactorySetup(): void
    {
        $orderStatus = OrderStatus::factory()->create(['title' => 'created']);
        $this->payment = Payment::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create();
        $this->product = $product;

        $this->userOrder = Order::factory()->create([
            'user_id' => $this->user->uuid,
            'order_status_id' => $orderStatus->uuid,
            'payment_id' => $this->payment->uuid,
            'products' => json_encode([$product]),
            'address' => null,
            'delivery_fee' => $this->faker->randomFloat(2, 0, 50),
            'amount' => $this->faker->randomFloat(2, 10, 1000),
        ]);

        $customUser = User::factory()->create();
        $product = Product::factory()->create();
        $this->customOrder = Order::factory()->create([
            'user_id' => $customUser->uuid,
            'order_status_id' => $orderStatus->uuid,
            'payment_id' => null,
            'products' => json_encode([$product]),
            'address' => json_encode([]),
            'delivery_fee' => $this->faker->randomFloat(2, 0, 50),
            'amount' => $this->faker->randomFloat(2, 10, 100),
        ]);

        $this->orderStatus = $orderStatus;
    }

    public function testUserCanGetTheirOwnOrders()
    {
        $this->defaultFactorySetup();
        $response = $this->actingAs($this->user)->getJson("/api/v1/order/{$this->userOrder->uuid}", [
            'Accept' => "application/json",
            'Authorization' => 'Bearer ' . $this->jwtUserToken
        ])
            ->assertOk()
            ->assertJsonPath('uuid', $this->userOrder->uuid);
        $this->assertNotEquals($this->customOrder->toArray(), $response->json());
    }

    public function testUserCanGetSpecificOrder()
    {
        $this->defaultFactorySetup();
        $this->actingAs($this->user)->getJson('/api/v1/order/' . $this->userOrder->uuid, [
            'Accept' => "application/json",
            'Authorization' => 'Bearer ' . $this->jwtUserToken
        ])
            ->assertOk();
    }

    public function testUserCannotGetOtherUserOrder()
    {
        $this->defaultFactorySetup();
        $this->actingAs($this->user)->getJson('/api/v1/order/' . $this->customOrder->uuid, [
            'Accept' => "application/json",
            'Authorization' => 'Bearer ' . $this->jwtUserToken
        ])
            ->assertStatus(404);
    }

    public function testUserCanCreateNewOrder()
    {
        $this->defaultFactorySetup();
        $data = [
            "order_status_uuid" => $this->orderStatus->uuid,
            'payment_uuid' => $this->payment->uuid,
            "products" => [
                [
                    "product" => $this->product->uuid,
                    "quantity" => 2
                ]
            ],
            "address" => [
                "billing" => fake()->address,
                "shipping" => fake()->address
            ],
            'delivery_fee' => $this->faker->randomFloat(2, 0, 50),
            'amount' => $this->faker->randomFloat(2, 10, 100),
        ];
       $this->actingAs($this->user)->postJson('/api/v1/order/create', $data, [
            'Accept' => "application/json",
            'Authorization' => 'Bearer ' . $this->jwtUserToken
        ])->assertOk()
            ->assertJsonFragment(['amount' => $data['amount']]);
    }

    public function testUserCanDeleteAnOrder()
    {
        $this->defaultFactorySetup();
        $this->actingAs($this->user)->deleteJson('/api/v1/order/' . $this->userOrder->uuid, [], [
            'Accept' => "application/json",
            'Authorization' => 'Bearer ' . $this->jwtUserToken
        ])
            ->assertOk();
    }

    public function testUserCanUpdateAnOrder()
    {
        $this->defaultFactorySetup();
        $orderStatus = OrderStatus::factory()->create(['title' => 'shipped']);
        $data = [
            "order_status_uuid" => $orderStatus->uuid,
            'payment_uuid' => $this->payment->uuid,
            "products" => [
                [
                    "product" => $this->product->uuid,
                    "quantity" => 2
                ]
            ],
            "address" => [
                "billing" => fake()->address,
                "shipping" => fake()->address
            ],
            'delivery_fee' => $this->faker->randomFloat(2, 0, 50),
            'amount' => $this->faker->randomFloat(2, 10, 100),
        ];

        $this->actingAs($this->user)->putJson('/api/v1/order/' . $this->userOrder->uuid, $data, [
            'Accept' => "application/json",
            'Authorization' => 'Bearer ' . $this->jwtUserToken
        ])->assertOk();
    }
}
