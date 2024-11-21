<?php

namespace App\Http\Controllers;

use App\Models\OrderBotSettings;
use App\Models\Shop;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShopifyBotController extends Controller
{

    public function updateShopifyBotStatus(){
        $user = Shop::where([
            ['user_id', '=', auth()->id()],
            ['shop_domain', '=', env('SHOPIFY_STORE_URL')]
        ])->first();

        if ($user) {
            $botStatus = OrderBotSettings::select('shopify_bot')->first();

            if ($botStatus) {
                $botStatus->shopify_bot = !$botStatus->shopify_bot;
                $botStatus->save();

                return back()->with([
                    'shopify_bot' => $botStatus->shopify_bot,
                    'message' => 'Bot status updated successfully',
                ]);
            } else {
                return back()->withErrors([
                    'message' => 'Bot status not found',
                ]);
            }

        }

        return back()->withErrors([
            'message' => 'User  not found',
        ]);
}



}
