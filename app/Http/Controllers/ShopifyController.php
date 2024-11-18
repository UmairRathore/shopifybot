<?php

namespace App\Http\Controllers;

use App\Models\OrderBotSettings;
use App\Models\Product;
use App\Models\Shop;
use App\Services\ShopifyService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Faker\Factory as Faker;


class ShopifyController extends Controller
{
    protected $shopifyService;

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function redirectToShopify()
    {
        return redirect($this->shopifyService->redirectToShopify());
    }

    public function handleCallback(Request $request)
    {
        return $this->shopifyService->handleCallback($request);
    }

}
