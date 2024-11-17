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
            dd($request);
    }


    public function generateOrders(Request $request)
    {

        // Step 1: Get the access token
        $accessToken = $this->shopifyService->redirectToShopify();
        if (!$accessToken) {
            Log::error('Access token retrieval failed.');
            return response()->json(['error' => 'Failed to obtain access token'], 400);
        }

        //Step 2: Fetch products
        $products = $this->shopifyService->fetchProducts($accessToken);

        // Step 3: Fetch OrderBotSettings (generate fake if not found)
        $settings = OrderBotSettings::first();

        if (!$settings) {
            Log::info('OrderBotSettings not found. Creating fake settings.');
            $faker = Faker::create();
            $settings = OrderBotSettings::create([
                'order_range_min' => $faker->numberBetween(1, 2),
                'order_range_max' => $faker->numberBetween(3, 5),
                'order_value_min' => $faker->randomFloat(2, 10, 50),
                'order_value_max' => $faker->randomFloat(2, 51, 150),
                'items_per_order_min' => 1,
                'items_per_order_max' => 3,
                'one_item_order_chance_min' => 20,
                'one_item_order_chance_max' => 80,
                'order_speed_min' => 1,
                'order_speed_max' => 3,
                'order_speed_unit' => 'seconds',
                'csv_file_path' => null
            ]);
            Log::info('Fake OrderBotSettings created.');
        }

        // Step 4: Generate orders
        $orderRange = rand($settings->order_range_min, $settings->order_range_max); // Get a random range for number of orders to create
        $customers = $this->generateFakeCustomers();

        for ($i = 0; $i < $orderRange; $i++) {
            $orderValue = rand($settings->order_value_min, $settings->order_value_max);
            $itemsPerOrder = rand($settings->items_per_order_min, $settings->items_per_order_max);
            $oneItemChance = rand($settings->one_item_order_chance_min, $settings->one_item_order_chance_max);
            $orderSpeed = rand($settings->order_speed_min, $settings->order_speed_max);

            $orderItems = [];
            for ($j = 0; $j < $itemsPerOrder; $j++) {
                $randomProduct = $products[array_rand($products)];
                $orderItems[] = [
                    'variant_id' => $randomProduct['variants'][0]['id'], // Assuming we are using the first variant
                    'quantity' => 1
                ];
            }


            $customer = $customers[array_rand($customers)];


            $orderPayload = [
                'order' => [
                    'line_items' => $orderItems,
                    'customer' => [
                        'first_name' => $customer['name'],
                        'email' => $customer['email'],
                        'address' => $customer['address'],
                    ],
                    'total_price' => $orderValue,
                    'financial_status' => 'paid',
                    'transactions' => [
                        [
                            'kind' => 'sale',
                            'status' => 'success',
                            'amount' => $orderValue,
                        ]
                    ]
                ]
            ];

            //Step 6: Create Orders
         $orderResponse = $this->shopifyService->generateOrder($orderPayload,$accessToken);


         $inventoryResponse = $this->updateInventory($orderResponse['order'],$customer,$orderValue,$orderItems,$accessToken);


// Step 7: Simulate order creation delay
            sleep($orderSpeed);
        }

        return response()->json(['status' => 'Orders generated successfully']);
    }


    private function generateFakeCustomers()
    {
        $faker = Faker::create();
        $customers = [];
        // Generate 5 fake customers for this example
        for ($i = 0; $i < 5; $i++) {
            $customers[] = [
                'name' => $faker->name,
                'email' => $faker->email,
                'address' => $faker->address
            ];
        }
        return $customers;
    }


    private function updateInventory($order, $customer, $orderValue, $orderItems, $accessToken)
    {
        if (isset($order)) {
            Log::info("Order created for customer: {$customer['name']} with order value: {$orderValue}");

            $responses = [];

            foreach ($orderItems as $item) {
                $variantId = $item['variant_id'];
                $quantitySold = $item['quantity'];

                $locationId = $this->shopifyService->fetchLocationId($accessToken);

                $inventoryResponse = $this->shopifyService->inventoryUpdate($variantId, $quantitySold, $accessToken, $locationId);

                Log::info("Inventory adjusted for variant ID: {$variantId}, quantity: -{$quantitySold}");

                if ($inventoryResponse) {
                    $responses[] = $inventoryResponse;
                } else {
                    Log::error("Failed to adjust inventory for variant ID: {$variantId}");
                }
            }

            return $responses;
        } else {
            Log::error("Failed to create order for customer: {$customer['name']}");
            return response()->json(['error' => 'Order creation failed'], 500);
        }
    }


}
