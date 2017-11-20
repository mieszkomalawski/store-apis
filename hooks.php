<?php
use Dredd\Hooks;

Hooks::before("/api/checkout/carts/{cartId}/products > POST > 201 > application/json", function(&$transaction) {

    $requestBody = json_decode($transaction->request->body, true);
    //fallout id
    $requestBody['product'] = '7dbaf7f6-c415-42cf-85c2-9a8fababcba6';
    $requestBody['quantity'] = 1;
    $transaction->request->body = json_encode($requestBody);
});