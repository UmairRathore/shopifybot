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
                // Toggle the bot status
                $botStatus->shopify_bot = !$botStatus->shopify_bot;
                $botStatus->save();

                // Return an Inertia response with the updated bot status
                return Inertia::render('shopifybot.index', [
                    'message' => 'Bot status updated successfully',
                    'shopify_bot' => $botStatus->shopify_bot
                ]);
            } else {
                return Inertia::render('shopifybot.index', [
                    'message' => 'Bot status not found',
                ]);
            }
        }

        return Inertia::render('shopifybot.index', [
            'message' => 'User Not found',
        ]);
}



}
