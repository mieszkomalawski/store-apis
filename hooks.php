<?php
use Dredd\Hooks;
use Ramsey\Uuid\Uuid;

Hooks::before("/api/checkout/carts/{cartId}/products > POST > 201 > application/json", function(&$transaction) {

    $requestBody = json_decode($transaction->request->body, true);
    $transaction->fullPath = '/api/checkout/carts/' . Uuid::fromString('3d73fbef-7998-4836-a521-004fdfbb0241') .  '/products';
    //fallout id
    $requestBody['product'] = \Ramsey\Uuid\Uuid::fromString('162e2dc2-6761-4a4e-9203-05f367d7ccd9');
    $transaction->request->body = json_encode($requestBody);
});