<?php

namespace App\Services;

use App\Models\OrderBotSettings;
use App\Models\Shop;
use Faker\Factory as Faker;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Api;

class OrderGenerate
{
    protected $shopifyService;
    protected $telegramChatID;
    protected $telegramBotToken;


    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
        $this->telegramChatID = env('TELEGRAM_CHAT_ID');
        $this->telegramBotToken = env('TELEGRAM_BOT_TOKEN');
    }


    public function generateOrders()
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

        $customers = $this->generateFakeCustomers($settings->csv_file_pat);
        $orderRange = rand($settings->order_range_min, $settings->order_range_max);

        for ($i = 0; $i < $orderRange; $i++) {

            $baseOrderValue = rand($settings->order_value_min, $settings->order_value_max);

            $randomCents = rand(0, 99);

            $orderValue = $baseOrderValue + ($randomCents / 100);

            if ($orderValue < $settings->order_value_min) {
                $orderValue += 100;
            } elseif ($orderValue > $settings->order_value_max) {
                $orderValue -= 100;
            }

            $itemsPerOrder = rand($settings->items_per_order_min, $settings->items_per_order_max);
            $oneItemChance = rand($settings->one_item_order_chance_min, $settings->one_item_order_chance_max);
            $orderSpeed = rand($settings->order_speed_min, $settings->order_speed_max);

            $orderItems = [];
            for ($j = 0; $j < $itemsPerOrder; $j++) {
                $randomProduct = $products[array_rand($products)];
                $orderItems[] = [
                    'variant_id' => $randomProduct['variants'][0]['id'],
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
                    'financial_status' => 'paid', // Indicates the payment is successful
                    'transactions' => [
                        [
                            'kind' => 'sale',
                            'status' => 'success',
                            'amount' => $orderValue,
                            'gateway' => 'manual', // You can set 'manual' for bank transfer
                            'payment_details' => [
                                'credit_card_number' => 'xxxx-xxxx-xxxx-xxxx', // Placeholder for manual payment method
                                'credit_card_expiry' => '01/23' // Optional
                            ]
                        ]
                    ]
                ]
            ];

            // Step 6: Create Orders
            $orderResponse = $this->shopifyService->generateOrder($orderPayload, $accessToken);

            Log::info($orderResponse);

            // Step 7: Notify via Telegram if enabled
            if ($settings->telegram_bot) {
                $telegramResponse = $this->telegramNotification($orderValue, $orderItems, $customer);
                Log::info($telegramResponse);
            } else {
                Log::info("Telegram Bot is disabled in bot order settings");
            }

            // Step 10: Simulate order creation delay
            sleep($orderSpeed);
        }


        return response()->json(['status' => 'Orders generated successfully']);
    }


    private function generateFakeCustomers($csvFilPath)
    {
        if ($csvFilPath && Storage::exists($csvFilPath)) {
            Log::info('CSV file found: ' . $csvFilPath);

            // Open the file for reading
            $file = Storage::path($csvFilPath);
            if (($handle = fopen($file, 'r')) !== false) {
                $header = fgetcsv($handle); // Read the first row as headers
                while (($row = fgetcsv($handle)) !== false) {
                    $customerData = array_combine($header, $row); // Combine headers with row values
                    $customers[] = [
                        'name' => $customerData['name'] ?? null,
                        'email' => $customerData['email'] ?? null,
                        'address' => $customerData['address'] ?? null,
                    ];
                }
                fclose($handle);
            }
        } else {
            Log::info('CSV file does not exist. Generating fake customers.');

            $faker = Faker::create();
            for ($i = 0; $i < 10; $i++) {
                $customers[] = [
                    'name' => $faker->name,
                    'email' => $faker->email,
                    'address' => $faker->address,
                ];
            }
        }

        return $customers;
    }


//    private function updateInventory($order, $customer, $orderValue, $orderItems, $accessToken)
//    {
//        if (isset($order)) {
//            Log::info("Order created for customer: {$customer['name']} with order value: {$orderValue}");
//
//            $responses = [];
//
//            foreach ($orderItems as $item) {
//                $variantId = $item['variant_id'];
//                $quantitySold = $item['quantity'];
//
//                $locationId = $this->shopifyService->fetchLocationId($accessToken);
//
//                $inventoryResponse = $this->shopifyService->inventoryUpdate($variantId, $quantitySold, $accessToken, $locationId);
//
//                Log::info("Inventory adjusted for variant ID: {$variantId}, quantity: -{$quantitySold}");
//
//                if ($inventoryResponse) {
//                    $responses[] = $inventoryResponse;
//                } else {
//                    Log::error("Failed to adjust inventory for variant ID: {$variantId}");
//                }
//            }
//
//            return $responses;
//        } else {
//            Log::error("Failed to create order for customer: {$customer['name']}");
//            return response()->json(['error' => 'Order creation failed'], 500);
//        }
//    }

    private function telegramNotification($orderValue, $orderItems, $customer)
    {
        $message  =  "New Order Generated:\n";
        $message .= "Customer: {$customer['name']}\n";
        $message .= "Email: {$customer['email']}\n";
        $message .= "Address: {$customer['address']}\n";
        $message .= "Order Value: $ {$orderValue}\n";
        $message .= "Items:\n";
        foreach ($orderItems as $item) {
            $message .= "- Product Variant ID: {$item['variant_id']} | Quantity: {$item['quantity']}\n";
        }

        $telegram = new Api($this->telegramBotToken);

        $chatId = $this->telegramChatID;

        try {
            // Sending the message to Telegram
            $response = $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
            ]);

            // If response is successful, log the result
            Log::info('Telegram message sent successfully', ['response' => $response]);
            print_r($telegram);
            return $message;

        } catch (\Telegram\Bot\Exceptions\TelegramSDKException $e) {
            // Log the error if sending failed
            Log::error('Failed to send message to Telegram', ['error' => $e->getMessage()]);
        }

    }

}



?>
