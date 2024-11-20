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


    public function storeSettings(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'file_location' => 'nullable|file|mimes:csv|max:2048', // Adjust validation rules as needed
        ]);



        if ($request->hasFile('file_location')) {
            $uploadedFile = $request->file('file_location');
            $csvFileName = $uploadedFile->getClientOriginalName();
            $csvFileExtension = $uploadedFile->getClientOriginalExtension();
            $csvFilePath = $uploadedFile->store('csv_files', 'public');
        }

        $settings = OrderBotSettings::updateOrCreate(
            [
                'order_range_min' => $request->order_range_min ?? '',
                'order_range_max' => $request->order_range_max ?? '',
                'order_value_min' => $request->order_value_min ?? '',
                'order_value_max' => $request->order_value_max ?? '',
                'items_per_order_min' => $request->items_per_order_min ?? '',
                'items_per_order_max' => $request->items_per_order_max ?? '',
                'one_item_order_chance_min' => $request->one_item_chance_min ?? '',
                'one_item_order_chance_max' => $request->one_item_chance_max ?? '',
                'order_speed_min' => $request->order_speed_min ?? '',
                'order_speed_max' => $request->order_speed_max ?? '',
                'order_speed_unit' => $request->order_frequency ?? '',
                'telegram_bot' => $request->telegram_bot ? true : false,
                'unlimited_orders' => $request->unlimited_orders ? true : false,
                'csv_file_path' => $csvFilePath,
                'csv_file_name' => $csvFileName,
                'csv_file_extension' => $csvFileExtension,
            ]
        );

        return response()->json([
            'message' => 'Settings updated successfully',
            'settings' => $settings,
        ]);
    }





}
