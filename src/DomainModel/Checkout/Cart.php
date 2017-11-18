<?php

declare(strict_types=1);

namespace Store\Checkout;

use DeepCopy\DeepCopy;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Store\Catalog\Product;

class Cart
{
    const ITEM_LIMIT = 3;
    /**
     * @var array
     */
    private $products = [];

    /**
     * @param Product $product
     * @param int $quantity
     */
    public function add(Product $product, int $quantity)
    {
        $id = $product->getId();
        $hasThisProduct = $this->findItemById($id);
        if (count($hasThisProduct) > 0
        ) {
            $this->increaseQuantity(current($hasThisProduct));
        } else {
            if (count($this->products) >= self::ITEM_LIMIT) {
                throw new \InvalidArgumentException('Cannot have more than 3 products in cart');
            }
            $this->addProduct($product, $quantity);
        }
    }

    public function getProducts(): iterable
    {
        $deepCopy = new DeepCopy();
        return $deepCopy->copy($this->products);
    }

    public function remove(Uuid $productId): void
    {
        $hasThisProduct = $this->findItemById($productId);
        if (count($hasThisProduct) > 0
        ) {
            /** @var CartItem $cartItem */
            $cartItem = current($hasThisProduct);
            unset($this->products[(string)$cartItem->getProductId()]);
        } else {
            throw new \InvalidArgumentException('Cannot remove product by id ' . $productId->toString() . ', product not found');
        }

    }

    /**
     * @return Money
     */
    public function total(): Money
    {
        return array_reduce($this->products, function ($carry, CartItem $cartItem) {
            $price = $cartItem->getPrice() ;
            if (null === $carry) {
                return $price;
            }
            /** @var Money $carry */
            return $carry->add($price);

        }, null);
    }

    /**
     * @param UuidInterface $productId
     * @return array
     */
    private function findItemById(UuidInterface $productId): array
    {
        return array_filter($this->products, function (CartItem $cartItem) use ($productId) {
            return (string)$cartItem->getProductId() === (string)$productId;
        });
    }

    private function increaseQuantity(CartItem $cartItem): void
    {
        $cartItem->increaseQuantity(1);
        $this->products[(string)$cartItem->getProductId()] = $cartItem;
    }

    /**
     * @param Product $product
     * @param int $quantity
     */
    private function addProduct(Product $product, int $quantity): void
    {
        $this->products[(string)$product->getId()] = new CartItem($product, $quantity);
    }

}
