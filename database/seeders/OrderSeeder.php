<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'admin@buckhill.co.uk')->first();
        Order::factory(700)->create();
        Order::factory(2)->create([
            'user_id' => $user->uuid,
        ]);
    }
}
