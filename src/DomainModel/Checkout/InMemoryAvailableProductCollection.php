<?php


namespace Store\Checkout;

use Ramsey\Uuid\UuidInterface;
use Store\Catalog\Product;

class InMemoryAvailableProductCollection implements AvailableProductCollection
{
    /**
     * @var array
     */
    private $products;

    /**
     * InMemoryAvialableProductCollection constructor.
     * @param array $products
     */
    public function __construct(array $products)
    {
        foreach ($products as $product) {
            $this->products[(string)$product->getId()] = $product;
        }
    }

    /**
     * @param UuidInterface $id
     * @return Product
     */
    public function getById(UuidInterface $id): Product
    {
        return $this->products[(string)$id];
    }
}
