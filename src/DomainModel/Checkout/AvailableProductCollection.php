<?php


namespace Store\Checkout;

use Ramsey\Uuid\UuidInterface;
use Store\Catalog\Product;

interface AvailableProductCollection
{
    public function getById(UuidInterface $id): Product;
}
