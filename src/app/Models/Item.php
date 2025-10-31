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
        'published_at',
    ];

    protected $casts = [
        'price' => 'integer',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item', 'item_id', 'category_id')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function favoredByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/placeholder-290.png');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset('storage/'.$this->image);
    }

    public function isSold(): bool
    {
        if ($this->relationLoaded('order')) {
            return ! is_null($this->order) || $this->status === 'sold';
        }

        return $this->order()->exists() || $this->status === 'sold';
    }

    public function getFavoritesCountAttribute(): int
    {
        return $this->favoredByUsers()->count();
    }

    public function getCommentsCountAttribute(): int
    {
        return $this->comments()->count();
    }
}
