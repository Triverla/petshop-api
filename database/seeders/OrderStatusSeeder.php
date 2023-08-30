<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statusTitles = ['open', 'pending_payment', 'paid', 'shipped', 'cancelled'];

        foreach ($statusTitles as $title) {
            OrderStatus::factory()->create(['title' => $title]);
        }
    }
}
