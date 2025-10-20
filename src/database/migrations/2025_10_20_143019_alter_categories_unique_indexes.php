<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // 旧: name 単体ユニークが残っているので落とす
            // インデックス名が 'categories_name_unique' のはず
            $table->dropUnique('categories_name_unique');

            // 新: 親別に同名OKにするため複合ユニークを付与
            $table->unique(['parent_id', 'name'], 'categories_parent_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // 差し戻し: 複合ユニークを外して name 単体ユニークを戻す
            $table->dropUnique('categories_parent_name_unique');
            $table->unique('name', 'categories_name_unique');
        });
    }
};