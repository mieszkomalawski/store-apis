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
    private $id;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var int
     */
    private $quantity;

    /**
     * CartItem constructor.
     * @param Product $product
     * @param int $quantity
     */
    public function __construct(UuidInterface $id, Cart $cart, Product $product, int $quantity)
    {
        $this->id = $id;
        $this->cart = $cart;
        if($quantity < 1){
            throw new \InvalidArgumentException('Cannot have negative amount of item');
        }
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return UuidInterface
     */
    public function getProductId(): UuidInterface
    {
        return $this->product->getId();
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->product->getPrice()->multiply($this->quantity);
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
        if($quantity < 1){
            throw new \InvalidArgumentException('Cannot increase quantity by negative offset');
        }
        $this->quantity += $quantity;
    }
}
