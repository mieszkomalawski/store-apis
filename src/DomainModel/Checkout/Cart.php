<?php

declare(strict_types=1);

namespace Store\Checkout;

use Doctrine\Common\Collections\ArrayCollection;
use Money\Money;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Store\Checkout\Event\CartCreated;
use Store\Checkout\Event\ProductAdded;
use Store\Checkout\Event\ProductRemoved;

class Cart extends AggregateRoot
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
    public static function create(UuidInterface $id)
    {
        $cart = new self();
        $cart->recordThat(CartCreated::create($id));
        return $cart;
    }

    protected function aggregateId(): string
    {
        if(!$this->id instanceof Uuid){
            return $this->id;
        }
        return $this->id->toString();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        if(!$this->id instanceof Uuid){
            return Uuid::fromString($this->id);
        }
        return $this->id;
    }

    /**
     * @param UuidInterface $productId
     * @param int $quantity
     */
    public function add(UuidInterface $productId, int $quantity)
    {
        $hasThisProduct = $this->findItemById($productId);
        if (!$hasThisProduct->isEmpty()
        ) {
            $this->recordThat(ProductAdded::create(
                $this->id,
                $productId,
                $quantity
            ));
        } else {
            if (count($this->products) >= self::ITEM_LIMIT) {
                throw new \InvalidArgumentException('Cannot have more than 3 products in cart');
            }
            $this->recordThat(ProductAdded::create(
                $this->id,
                $productId,
                $quantity
            ));
        }
    }

    public function remove(UuidInterface $productId): void
    {
        $hasThisProduct = $this->findItemById($productId);
        if (!$hasThisProduct->isEmpty()
        ) {
            $this->recordThat(ProductRemoved::create(
                $this->id,
                $productId
            ));
        } else {
            throw new \InvalidArgumentException('Cannot remove product by id ' . $productId->toString() . ', product not found');
        }
    }

    public function getProducts(): iterable
    {
        return $this->products->map(function (CartItem $cartItem) {
            return $cartItem->getProductId();
        });
    }

    /**
     * @return Money
     */
    public function getTotal(AvailableProductCollection $availableProductCollection): Money
    {
        return array_reduce($this->products->toArray(), function ($carry, CartItem $cartItem)use($availableProductCollection) {
            $product = $availableProductCollection->getById($cartItem->getProductId());
            $price = $product->getPrice();
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
     * @param UuidInterface $productId
     * @param int $quantity
     */
    private function addProduct(UuidInterface $productId, int $quantity): void
    {
        $this->products->add(new CartItem($productId, $quantity));
    }

    protected function apply(AggregateChanged $event): void
    {
        switch (get_class($event)) {
            case CartCreated::class:
                /** @var CartCreated $event */
                $this->id = Uuid::fromString($event->getId());
                $this->products = new ArrayCollection();
                break;
            case ProductAdded::class:
                /** @var ProductAdded $event */
                $id = $event->getProductId();
                $hasThisProduct = $this->findItemById($id);
                if (!$hasThisProduct->isEmpty()
                ) {
                    $this->increaseQuantity($hasThisProduct->first());
                } else {
                    $this->addProduct($id, $event->getQuantity());
                }
                break;
            case ProductRemoved::class:
                /** @var ProductRemoved $event */
                $cartItem = $this->findItemById($event->getProductId())->first();
                $this->products->removeElement($cartItem);
                break;
        }
    }
}
