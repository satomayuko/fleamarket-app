<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('profiles', 'postal_code')) {
                $table->string('postal_code', 8)->nullable()->after('avatar_path');   // 例: 123-4567
            }
            if (!Schema::hasColumn('profiles', 'address')) {
                $table->string('address', 255)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('profiles', 'building')) {
                $table->string('building', 255)->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (Schema::hasColumn('profiles', 'building')) {
                $table->dropColumn('building');
            }
            if (Schema::hasColumn('profiles', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('profiles', 'postal_code')) {
                $table->dropColumn('postal_code');
            }
        });
    }
};