<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBotSettings extends Model
{
    use HasFactory;

    protected $table = 'order_bot_settings';
    protected $fillable = [
        'order_range_min',
        'order_range_max',
        'order_value_min',
        'order_value_max',
        'items_per_order_min',
        'items_per_order_max',
        'one_item_order_chance_min',
        'one_item_order_chance_max',
        'order_speed_min',
        'order_speed_max',
        'order_speed_unit',
        'csv_file_path',
    ];
}
