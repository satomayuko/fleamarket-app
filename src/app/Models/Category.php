<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
    ];

    // 親カテゴリ
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // 子カテゴリ
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // 商品（多対多に修正）
    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_item')->withTimestamps();
    }
}