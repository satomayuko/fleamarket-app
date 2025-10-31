<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // $this->call(UserSeeder::class);
        User::updateOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => '出品太郎',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'buyer@example.com'],
            [
                'name' => '購入花子',
                'password' => Hash::make('password'),
            ]
        );

        User::factory()->count(8)->create();
    }
}
