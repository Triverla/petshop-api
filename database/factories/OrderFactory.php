<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::whereId(rand(1, 100))->first();
        $orderStatus = OrderStatus::all();
        $orderStatusPicked = $orderStatus->random();
        $payment = Payment::where('id', rand(1, 20))->first();

        $statuses = ['open' => 'open', 'pending_payment' => 'pending_payment', 'cancelled' => 'cancelled'];
        $numberOfProducts = rand(1, 2);

        $products = Product::inRandomOrder()
            ->take($numberOfProducts)
            ->get()
            ->map(function ($product) {
                return [
                    'product' => $product->uuid,
                    'quantity' => rand(1, 5),
                ];
            })
            ->toArray();

        $orderAmount = $this->faker->randomFloat(2, 10, 1000);
        $deliveryFee = $orderAmount > 500 ? 15 : 0;

        return [
            'user_id' => $user->uuid,
            'order_status_id' => $orderStatusPicked->uuid,
            'payment_id' => isset($statuses[$orderStatusPicked->title]) ? null : $payment->uuid,
            'products' => json_encode($products),
            'address' => json_encode(
                [
                    'billing' => fake()->address(),
                    'shipping' => fake()->address(),
                ]
            ),
            'delivery_fee' => $deliveryFee,
            'amount' => $orderAmount,
            'shipped_at' => $this->faker->randomElement([null, now(), now()->addDays(3), now()->subDays(2)])
        ];
    }
}
