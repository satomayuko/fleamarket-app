<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('item_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('buyer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedInteger('price_at_purchase');
            $table->string('status', 20)->default('pending');
            $table->timestamps();

            $table->unique('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}