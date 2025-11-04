<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderAddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 購入後に住所スナップショットが注文に保存される()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $item = Item::create([
            'user_id' => $seller->id,
            'name' => 'テスト商品',
            'description' => '説明',
            'price' => 1500,
            'image' => 'items/dummy.jpg',
            'brand' => null,
            'condition' => '新品',
            'status' => 'selling',
            'published_at' => now(),
        ]);

        DB::table('addresses')->insert([
            'user_id' => $buyer->id,
            'zip' => '123-4567',
            'address' => '東京都 渋谷区 神南1-2-3',
            'building' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($buyer)
            ->get(route('orders.success', ['item' => $item->id]))
            ->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'status' => 'paid',
            'ship_zip' => '123-4567',
            'ship_address' => '東京都 渋谷区 神南1-2-3',
            'ship_building' => null,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);
    }
}