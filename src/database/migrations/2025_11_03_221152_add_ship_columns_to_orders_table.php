<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('orders', 'ship_zip')) {
                $table->string('ship_zip', 10)->nullable()->after('item_id');
            }
            if (! Schema::hasColumn('orders', 'ship_address')) {
                $table->string('ship_address', 255)->nullable()->after('ship_zip');
            }
            if (! Schema::hasColumn('orders', 'ship_building')) {
                $table->string('ship_building', 255)->nullable()->after('ship_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            if (Schema::hasColumn('orders', 'ship_building')) {
                $table->dropColumn('ship_building');
            }
            if (Schema::hasColumn('orders', 'ship_address')) {
                $table->dropColumn('ship_address');
            }
            if (Schema::hasColumn('orders', 'ship_zip')) {
                $table->dropColumn('ship_zip');
            }
        });
    }
};