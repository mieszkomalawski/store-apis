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
use Store\Checkout\Exception\CannotRemoveProductException;
use Store\Checkout\Exception\TooManyProductsException;
use Store\SharedKernel\MoneyFactory;

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
     * @return Cart
     */
    public static function create(UuidInterface $id): Cart
    {
        $cart = new self();
        $cart->recordThat(CartCreated::create($id));
        return $cart;
    }

    /**
     * @return string
     */
    protected function aggregateId(): string
    {
        if (!$this->id instanceof Uuid) {
            return $this->id;
        }
        return $this->id->toString();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        if (!$this->id instanceof Uuid) {
            return Uuid::fromString($this->id);
        }
        return $this->id;
    }

    /**
     * @param UuidInterface $productId
     */
    public function add(UuidInterface $productId): void
    {
        if (count($this->products) >= self::ITEM_LIMIT) {
            throw TooManyProductsException::create();
        }
        $this->recordThat(ProductAdded::create(
            $this->id,
            $productId
        ));
    }

    /**
     * @param UuidInterface $productId
     * @throws CannotRemoveProductException
     */
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
            throw CannotRemoveProductException::create($productId);
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getProducts(): ArrayCollection
    {
        /**
         * We don't want someone to bypass aggregate (cart)
         */
        return clone $this->products;
    }

    /**
     * @return Money
     */
    public function getTotal(AvailableProductCollection $availableProductCollection): Money
    {
        return array_reduce(
            $this->products->toArray(),
            function ($carry, UuidInterface $cartItem) use ($availableProductCollection) {
                $product = $availableProductCollection->getById($cartItem);
                $price = $product->getPrice();
                /** @var Money $carry */
                return $carry->add($price);
            },
            MoneyFactory::USD(0)
        );
    }

    /**
     * @param UuidInterface $productId
     * @return ArrayCollection
     */
    private function findItemById(UuidInterface $productId): ArrayCollection
    {
        return $this->products->filter(function (UuidInterface $cartItem) use ($productId) {
            return (string)$cartItem === (string)$productId;
        });
    }

    protected function apply(AggregateChanged $event): void
    {
        switch (get_class($event)) {
            case CartCreated::class:
                /** @var CartCreated $event */
                $this->applyCreate($event);
                break;
            case ProductAdded::class:
                /** @var ProductAdded $event */
                $this->applyProductAdded($event);
                break;
            case ProductRemoved::class:
                /** @var ProductRemoved $event */
                $this->applyProductRemoved($event);
                break;
        }
    }

    /**
     * @param CartCreated $event
     */
    protected function applyCreate(CartCreated $event): void
    {
        $this->id = Uuid::fromString($event->getId());
        $this->products = new ArrayCollection();
    }

    /**
     * @param ProductAdded $event
     */
    protected function applyProductAdded(ProductAdded $event): void
    {
        $this->products->add($event->getProductId());
    }

    /**
     * @param ProductRemoved $event
     */
    protected function applyProductRemoved(ProductRemoved $event): void
    {
        $cartItem = $this->findItemById($event->getProductId())->first();
        $this->products->removeElement($cartItem);
    }
}
