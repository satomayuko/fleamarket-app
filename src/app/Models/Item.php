<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id','category_id','name','description','price',
        'condition','shipping_fee_burden','status','image','published_at'
    ];

    public function user() { return $this->belongsTo(User::class); }

    public function category() { return $this->belongsTo(Category::class); }

    public function comments() { return $this->hasMany(Comment::class); }

    public function order() { return $this->hasOne(Order::class); }

    public function favoredByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')
                    ->withTimestamps();
    }
}
