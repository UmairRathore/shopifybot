<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $table = 'shops';
    protected $fillable = [
        'shop_domain',
        'access_token',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
