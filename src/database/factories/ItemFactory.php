<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(300, 20000),
            'image' => 'items/dummy.jpg',
            'brand' => null,
            'condition' => '新品',
            'status' => 'selling',
            'published_at' => now(),
        ];
    }
}
