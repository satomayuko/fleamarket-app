<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('zip', 10);          // 例: 123-4567
            $table->string('address', 255);     // 都道府県 市区町村 番地 など
            $table->string('building', 255)->nullable(); // 建物名

            $table->timestamps();

            $table->unique(['item_id', 'user_id']); // 1ユーザーにつき1商品の配送先は1件
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_addresses');
    }
};