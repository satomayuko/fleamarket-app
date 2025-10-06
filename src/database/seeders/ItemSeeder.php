<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // 出品者ユーザーを用意（ID=1がなければ作成）
        $user = User::find(1);
        if (!$user) {
            $user = User::create([
                'name' => '出品者1',
                'email' => 'seller@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        // カテゴリを用意（なければ1件作成）
        $category = Category::first();
        if (!$category) {
            $category = Category::create([
                'name' => 'その他',
                // スキーマにslug等がなければ name だけでOK
            ]);
        }

        $rows = [
            //  title,     price, brand,      description,                                       imageUrl,                                                                 condition
            ['腕時計',     15000, 'Rolax',     'スタイリッシュなデザインのメンズ腕時計',               'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',         '良好'],
            ['HDD',         5000, '西芝',      '高速で信頼性の高いハードディスク',                   'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',             '目立った傷や汚れなし'],
            ['玉ねぎ3束',     300, 'なし',      '新鮮な玉ねぎ3束のセット',                           'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',                'やや傷や汚れあり'],
            ['革靴',         4000, null,       'クラシックなデザインの革靴',                         'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg','状態が悪い'],
            ['ノートPC',    45000, null,       '高性能なノートパソコン',                             'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',        '良好'],
            ['マイク',        8000, 'なし',      '高音質のレコーディング用マイク',                     'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',         '目立った傷や汚れなし'],
            ['ショルダーバッグ', 3500, null,     'おしゃれなショルダーバッグ',                       'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',      'やや傷や汚れあり'],
            ['タンブラー',      500, 'なし',      '使いやすいタンブラー',                             'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',          '状態が悪い'],
            ['コーヒーミル',    4000, 'Starbacks','手動のコーヒーミル',                              'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg','良好'],
            ['メイクセット',    2500, null,       '便利なメイクアップセット',                         'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg','目立った傷や汚れなし'],
        ];

        foreach ($rows as [$name,$price,$brand,$desc,$image,$condition]) {
            $descWithBrand = $desc . ($brand && $brand !== 'なし' ? "（ブランド: {$brand}）" : '');

            Item::create([
                'user_id'             => $user->id,
                'category_id'         => $category->id,
                'name'               => $name,
                'description'         => $descWithBrand,
                'price'               => (int)$price,
                'condition'           => $condition,
                'shipping_fee_burden' => 'seller',     // 必須カラムにダミー値
                'status'              => 'selling',    // 既定
                'image'         => $image,       // 外部URLをそのまま保存
                'brand'               => $brand && $brand !== 'なし' ? $brand : null,
                'shipping_fee_burden' => 'seller',
                'status'              => 'selling',
                'published_at'        => now(),
            ]);
        }
    }
}