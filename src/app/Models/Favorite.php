<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $table = 'favorites';
    public $incrementing = false;
    protected $primaryKey = null;
    protected $fillable = ['user_id','item_id'];
    protected $keyType = 'int';

    public function user() { return $this->belongsTo(User::class); }
    public function item() { return $this->belongsTo(Item::class); }
}
