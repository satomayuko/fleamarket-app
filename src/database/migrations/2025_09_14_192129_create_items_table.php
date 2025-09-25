<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
             $table->foreignId('user_id')->constrained()->cascadeOnDelete();       // 出品者
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->unsignedInteger('price'); // 円
            $table->string('condition', 30);  // 例: new/used...
            $table->string('shipping_fee_burden', 20); // 例: seller/buyer
            $table->string('status', 20)->default('selling'); // selling / sold
            $table->string('cover_image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
}
