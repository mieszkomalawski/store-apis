<?php


namespace Store\Checkout\Event;


use Prooph\EventSourcing\AggregateChanged;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ProductAdded extends AggregateChanged
{
    public static function create(
        UuidInterface $cartId,
        UuidInterface $productId
    ) {
        return static::occur(
            (string)$cartId,
            [
                'productId' => (string)$productId
            ]
        );
    }

    public function getId(): UuidInterface
    {
        return Uuid::fromString($this->aggregateId());
    }

    public function getProductId(): UuidInterface
    {
        return Uuid::fromString($this->payload['productId']);
    }
}