<?php

declare(strict_types=1);

namespace Store\Catalog;

use Money\Money;
use Ramsey\Uuid\UuidInterface;

class Product
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Money
     */
    private $price;

    /**
     * Product constructor.
     * @param UuidInterface $id
     * @param string $name
     * @param Money $price
     */
    public function __construct(UuidInterface $id, string $name, Money $price)
    {
        if ($price->getAmount() < 0) {
            throw new \InvalidArgumentException('Product price cannot be negative');
        }
        if (empty($name)) {
            throw new \InvalidArgumentException('Name cannot be blank');
        }
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Money
     */
    public function getPrice(): Money
    {
        return $this->price;
    }
}
