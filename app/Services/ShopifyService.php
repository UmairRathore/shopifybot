<?php

namespace App\Services;

use App\Models\Shop;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ShopifyService
{
    protected $shopDomain;
    protected $client_id;
    protected $redirectUri;
    protected $clientSecret;
    protected $shopScopes;
    protected $clientHTTP;

    public function __construct()
    {
        $this->shopDomain = env('SHOPIFY_STORE_URL');
        $this->client_id = env('SHOPIFY_CLIENT_ID');
        $this->redirectUri = env('SHOPIFY_REDIRECT_URI');
        $this->clientSecret = env('SHOPIFY_CLIENT_SECRET');
        $this->shopScopes = env('SHOPIFY_SCOPES');

        $this->clientHTTP = new Client();
    }

    public function redirectToShopify()
    {
        Log::info('Redirecting user to Shopify for authentication.');

        $shop = Shop::where('shop_domain', $this->shopDomain)->first();

        if ($shop && $shop->updated_at->diffInHours(now()) < 1) {
            Log::info('Access token is still valid; no need to regenerate.' . ' TOKEN:' . $shop->access_token);
            return $shop->access_token;
        }

        $url = "https://{$this->shopDomain}/admin/oauth/authorize?client_id={$this->client_id}&scope=" . urlencode($this->shopScopes) . "&redirect_uri=" . urlencode($this->redirectUri);
        return $url;
    }

    public function handleCallback($request)
    {
        Log::info('Handling callback from Shopify.');

        // Extract parameters from the request sent by Shopify
        $shop_domain = $request->input('shop'); // The shop domain (e.g., testingdev050.myshopify.com)
        $code = $request->input('code');
        $hmac = $request->input('hmac');

        // Verify HMAC for security
        $params = $request->except('hmac');
        ksort($params);
        $computedHmac = hash_hmac('sha256', http_build_query($params), $this->clientSecret);

        if (!hash_equals($hmac, $computedHmac)) {
            Log::error('HMAC verification failed.');
            return response()->json(['error' => 'HMAC verification failed'], 400);
        }

        // Make the request to get the access token
        Log::info("Making request to get access token for shop: {$shop_domain}");
        $response = $this->clientHTTP->post("https://{$shop_domain}/admin/oauth/access_token", [
            'form_params' => [
                'client_id' => $this->client_id,
                'client_secret' => $this->clientSecret,
                'code' => $code,
            ]
        ]);

        // Extract the access token from the response
        $accessToken = json_decode($response->getBody()->getContents(), true)['access_token'];

        Log::info("Access token obtained: {$accessToken}");

        // Store the access token in the database (secure approach)
      Shop::updateOrCreate(
            ['shop_domain' => $shop_domain, 'user_id' => auth()->user()->id],
            [
                'access_token' => $accessToken,
                'updated_at' => now()
            ]
        );

        Log::info('App installed successfully.');

        return response()->json(['access_token' => $accessToken]);
    }

    public function fetchProducts($accessToken)
    {
        $response = $this->clientHTTP->get("https://{$this->shopDomain}/admin/api/2023-10/products.json", [
            'headers' => [
                'X-Shopify-Access-Token' => $accessToken,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true)['products'];
    }


    public function generateOrder($orderPayload,$accessToken)
    {
        $createOrderResponse = $this->clientHTTP->post("https://{$this->shopDomain}/admin/api/2023-10/orders.json", [
            'json' => $orderPayload,
            'headers' => [
                'X-Shopify-Access-Token' => $accessToken,
            ]
        ]);

        return json_decode($createOrderResponse->getBody()->getContents(), true);
    }


    public function inventoryUpdate($variantId, $quantitySold, $accessToken, $locationId)
    {
        try {
            $inventoryResponse = $this->clientHTTP->post("https://{$this->shopDomain}/admin/api/2023-10/variants/{$variantId}/adjust_inventory_level.json", [
                'json' => [
                    'inventory_adjustment' => [
                        'location_id' => $locationId,
                        'available_adjustment' => -$quantitySold,
                    ]
                ],
                'headers' => [
                    'X-Shopify-Access-Token' => $accessToken,
                    'Content-Type' => 'application/json',  // Explicitly set the content type

                ]
            ]);

            if ($inventoryResponse->getStatusCode() >= 200 && $inventoryResponse->getStatusCode() < 300) {
                $responseBody = json_decode($inventoryResponse->getBody()->getContents(), true);
                return $responseBody;
            } else {

                Log::error('Shopify inventory adjustment failed', [
                    'status' => $inventoryResponse->getStatusCode(),
                    'response' => $inventoryResponse->getBody()->getContents(),
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Error adjusting Shopify inventory', ['exception' => $e->getMessage()]);
            return null;
        }
    }


    public function fetchLocationId($accessToken)
    {
        // Make a request to Shopify to get the list of locations
        $response = $this->clientHTTP->get("https://{$this->shopDomain}/admin/api/2023-10/locations.json", [
            'headers' => [
                'X-Shopify-Access-Token' => $accessToken,
            ]
        ]);

        $locations = json_decode($response->getBody()->getContents(), true);
        if (isset($locations['locations']) && count($locations['locations']) > 0) {
            return $locations['locations'][0]['id'];
        }

        return null;
    }

}



?>
