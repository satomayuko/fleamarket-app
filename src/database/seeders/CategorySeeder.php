<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'ファッション' => ['レディース', 'メンズ', 'アクセサリー'],
            '家電' => ['キッチン', 'ゲーム'],
            'インテリア' => ['ハンドメイド'],
            'コスメ' => [],
            '本' => [],
            'スポーツ' => [],
            'おもちゃ' => [],
            'ベビー・キッズ' => [],
        ];

        foreach ($categories as $parentName => $children) {
            // 親カテゴリを登録
            $parent = Category::firstOrCreate([
                'name' => $parentName,
                'parent_id' => null,
            ]);

            // 子カテゴリを登録
            foreach ($children as $childName) {
                Category::firstOrCreate([
                    'name' => $childName,
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}
