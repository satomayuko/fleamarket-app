<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderAddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 購入後に住所が注文に紐づいて登録される()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        $item = Item::create([
            'user_id'   => $seller->id,
            'name'      => 'テスト商品',
            'description' => '説明',
            'price'     => 1500,
            'image'     => 'items/dummy.jpg',
            'brand'     => null,
            'condition' => '新品',
            'status'    => 'selling',
            'published_at' => now(),
        ]);

        $addrId = DB::table('item_addresses')->insertGetId([
            'item_id'    => $item->id,
            'user_id'    => $buyer->id,
            'zip'        => '123-4567',
            'address'    => '東京都渋谷区神南1-2-3',
            'building'   => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($buyer)
            ->get(route('orders.success', ['item' => $item->id]))
            ->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('orders', [
            'item_id'             => $item->id,
            'buyer_id'            => $buyer->id,
            'shipping_address_id' => $addrId,
            'status'              => 'paid',
        ]);

        $this->assertDatabaseHas('items', [
            'id'     => $item->id,
            'status' => 'sold',
        ]);
    }
}