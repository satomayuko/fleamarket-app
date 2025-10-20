<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'brand',
        'condition',
        'shipping_fee_burden',
        'status',
        'image',
        'published_at'
    ];

    protected $casts = [
        'price'        => 'integer',
        'published_at' => 'datetime',
    ];

    // 出品者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // カテゴリ（単一 → 複数に修正）
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item')->withTimestamps();
    }

    // コメント
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 注文（1対1）
    public function order()
    {
        return $this->hasOne(Order::class);
    }

    // いいね（多対多）
    public function favoredByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // 画像URL
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/placeholder-290.png');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    // 購入済み判定
    public function isSold(): bool
    {
        if ($this->relationLoaded('order')) {
            return !is_null($this->order) || $this->status === 'sold';
        }

        return $this->order()->exists() || $this->status === 'sold';
    }

    // いいね数
    public function getFavoritesCountAttribute(): int
    {
        return $this->favoredByUsers()->count();
    }

    // コメント数
    public function getCommentsCountAttribute(): int
    {
        return $this->comments()->count();
    }
}