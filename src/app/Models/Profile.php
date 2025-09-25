<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = ['postal_code','address','building','avatar_path','user_id'];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
}
