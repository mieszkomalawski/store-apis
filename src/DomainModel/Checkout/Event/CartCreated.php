<?php


namespace Store\Checkout\Event;

use Prooph\EventSourcing\AggregateChanged;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CartCreated extends AggregateChanged
{
    public static function create(
        UuidInterface $cartId
    ): AggregateChanged {
        return static::occur(
            (string)$cartId,
            []
        );
    }

    public function getId(): string
    {
        return Uuid::fromString($this->aggregateId());
    }
}
