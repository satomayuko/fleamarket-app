<?php

namespace Database\Seeders;

use Database\Seeders\ItemSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ItemSeeder::class,
        ]);
    }
}
