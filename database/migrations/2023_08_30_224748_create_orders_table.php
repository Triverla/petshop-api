<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique()->index();
            $table->uuid('order_status_id');
            $table->foreign('order_status_id')->references('uuid')->on('order_statuses');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('uuid')->on('users');
            $table->uuid('payment_id')->nullable();
            $table->foreign('payment_id')->references('uuid')->on('payments');
            $table->jsonb('products')->nullable();
            $table->jsonb('address')->nullable();
            $table->double('delivery_fee', 8, 2)->nullable();
            $table->double('amount', 8, 2);
            $table->timestamps();
            $table->timestamp('shipped_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
