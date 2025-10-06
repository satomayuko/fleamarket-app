<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 出品者
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete(); // カテゴリ任意
            $table->string('name', 120); // ← title → name に変更
            $table->text('description')->nullable();
            $table->unsignedInteger('price');
            $table->string('condition', 30);
            $table->string('shipping_fee_burden', 20)->nullable(); // 任意に
            $table->string('status', 20)->default('selling');
            $table->string('image')->nullable(); // ← cover_image → image に変更
            $table->string('brand', 100)->nullable(); // Seederのbrandにも対応
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
}