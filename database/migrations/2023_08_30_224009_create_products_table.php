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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique()->index();
            $table->string('title');
            $table->text('description')->nullable();
            $table->float('price', 8, 2);
            $table->jsonb('metadata')->nullable();
            $table->uuid('category_uuid');
            $table->foreign('category_uuid')->references('uuid')->on('categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
