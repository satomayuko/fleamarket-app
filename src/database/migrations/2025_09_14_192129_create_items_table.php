<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->unsignedInteger('price');
            $table->string('condition', 30);
            $table->string('shipping_fee_burden', 20)->nullable();
            $table->string('status', 20)->default('selling');
            $table->string('image')->nullable();
            $table->string('brand', 100)->nullable();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};