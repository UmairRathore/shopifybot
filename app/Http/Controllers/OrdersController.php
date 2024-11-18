<?php

namespace App\Http\Controllers;

use App\Models\OrderBotSettings;
use App\Services\ShopifyService;
use Faker\Factory as Faker;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    //

    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }


    public function storeOrderBotSetting(Request $request)
    {

        $settings = OrderBotSettings::create([
            'order_range_min' =>$request->order_range_min  ?? '',
            'order_range_max' => $request->order_range_max  ?? '',
            'order_value_min' => $request->order_value_min  ?? '',
            'order_value_max' => $request->order_value_max  ?? '',
            'items_per_order_min' => $request->items_per_order_max  ?? '',
            'items_per_order_max' => $request->items_per_order_min  ?? '',
            'one_item_order_chance_min' => $request->one_item_chance_max  ?? '',
            'one_item_order_chance_max' => $request->one_item_chance_min  ?? '',
            'order_speed_min' => $request->order_speed_min  ?? '',
            'order_speed_max' => $request->order_speed_max  ?? '',
            'order_speed_unit' => $request->order_frequency  ?? '',
            'telegramBot' => $request->telegramBot  ?? '',
            'unlimitedOrders' => $request->unlimitedOrders  ?? '',
            'csv_file_path' => $request->file_location ?? '',
        ]);

        dd($settings->order_speed_unit);
    }





}
