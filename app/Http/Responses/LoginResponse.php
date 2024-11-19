<?php

namespace App\Http\Responses;


use App\Services\ShopifyService;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{

    public function __construct(ShopifyService $shopifyService)
    {
        $this->shopifyService = $shopifyService;
    }

    public function toResponse($request)
    {
        return $this->shopifyService->redirectToShopify();
    }

}
