<?php

declare(strict_types=1);

namespace Store\Checkout;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Store\Catalog\Product;

class Cart
{
    const ITEM_LIMIT = 3;
    /**
     * @var ArrayCollection
     */
    private $products = [];

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * Cart constructor.
     * @param UuidInterface $id
     */
    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
        $this->products = new ArrayCollection();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @param Product $product
     * @param int $quantity
     */
    public function add(Product $product, int $quantity)
    {
        $id = $product->getId();
        $hasThisProduct = $this->findItemById($id);
        if (!$hasThisProduct->isEmpty()
        ) {
            $this->increaseQuantity($hasThisProduct->first());
        } else {
            if (count($this->products) >= self::ITEM_LIMIT) {
                throw new \InvalidArgumentException('Cannot have more than 3 products in cart');
            }
            $this->addProduct($product, $quantity);
        }
    }

    public function getProducts(): iterable
    {
        /**
         * We are cloning object to avoid situation where
         * someone modifies CartItems bypassing Cart itself (aggregate invariants)
         * shallow copy is enough as we do not have to worry about related entities eg. Product
         */
        return $this->products->map(function(CartItem $cartItem){
            return clone $cartItem;
        });
    }

    public function remove(UuidInterface $productId): void
    {
        $hasThisProduct = $this->findItemById($productId);
        if (!$hasThisProduct->isEmpty()
        ) {
            /** @var CartItem $cartItem */
            $cartItem = $hasThisProduct->first();
            $this->products->removeElement($cartItem);
        } else {
            throw new \InvalidArgumentException('Cannot remove product by id ' . $productId->toString() . ', product not found');
        }

    }

    /**
     * @return Money
     */
    public function getTotal(): Money
    {
        return array_reduce($this->products->toArray(), function ($carry, CartItem $cartItem) {
            $price = $cartItem->getPrice();
            /** @var Money $carry */
            return $carry->add($price);

        }, Money::USD(0));
    }

    /**
     * @param UuidInterface $productId
     * @return ArrayCollection
     */
    private function findItemById(UuidInterface $productId): ArrayCollection
    {
        return $this->products->filter(function (CartItem $cartItem) use ($productId) {
            return (string)$cartItem->getProductId() === (string)$productId;
        });
    }

    private function increaseQuantity(CartItem $cartItem): void
    {
        $cartItem->increaseQuantity(1);
    }

    /**
     * @param Product $product
     * @param int $quantity
     */
    private function addProduct(Product $product, int $quantity): void
    {
        $this->products->add(new CartItem(Uuid::uuid4(), $this, $product, $quantity));
    }

}
