<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $hasSingle = collect(DB::select("SHOW INDEX FROM `categories` WHERE Key_name = 'categories_name_unique'"))->isNotEmpty();
        if ($hasSingle) {
            DB::statement('DROP INDEX `categories_name_unique` ON `categories`');
        }

        $hasComposite = collect(DB::select("SHOW INDEX FROM `categories` WHERE Key_name = 'categories_parent_id_name_unique'"))->isNotEmpty();
        if (! $hasComposite) {
            Schema::table('categories', function (Blueprint $table): void {
                $table->unique(['parent_id', 'name'], 'categories_parent_id_name_unique');
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $hasComposite = collect(DB::select("SHOW INDEX FROM `categories` WHERE Key_name = 'categories_parent_id_name_unique'"))->isNotEmpty();
        if ($hasComposite) {
            Schema::table('categories', function (Blueprint $table): void {
                $table->dropUnique('categories_parent_id_name_unique');
            });
        }

        $hasSingle = collect(DB::select("SHOW INDEX FROM `categories` WHERE Key_name = 'categories_name_unique'"))->isNotEmpty();
        if (! $hasSingle) {
            Schema::table('categories', function (Blueprint $table): void {
                $table->unique('name', 'categories_name_unique');
            });
        }
    }
};