<?php

declare(strict_types=1);

namespace Store\Checkout;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use Store\Catalog\Product;

class CartItem
{
    /**
     * @var UuidInterface
     */
    private $productId;

    /**
     * @var int
     */
    private $quantity;

    /**
     * CartItem constructor.
     * @param Product $product
     * @param int $quantity
     */
    public function __construct(UuidInterface $productId, int $quantity)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Cannot have negative amount of item');
        }
        $this->productId = $productId;
        $this->quantity = $quantity;
    }

    /**
     * @return UuidInterface
     */
    public function getProductId(): UuidInterface
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function increaseQuantity(int $quantity)
    {
        if ($quantity < 1) {
            throw new \InvalidArgumentException('Cannot increase quantity by negative offset');
        }
        $this->quantity += $quantity;
    }
}
