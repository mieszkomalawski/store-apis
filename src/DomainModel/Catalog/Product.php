<?php

declare(strict_types=1);

namespace Store\Catalog;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use Store\Catalog\Exception\InvalidNameException;
use Store\Catalog\Exception\InvalidPriceException;

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
        $this->validatePrice($price);
        $this->validateName($name);
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    /**
     * @param Money $price
     */
    public function changePrice(Money $price): void
    {
        $this->validatePrice($price);
        $this->price = $price;
    }

    /**
     * @param string $name
     */
    public function changeName(string $name): void
    {
        $this->validateName($name);
        $this->name = $name;
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

    /**
     * Return normalized amount
     * @return float
     */
    public function getPriceDecimal(): float
    {
        return $this->price->getAmount() / 100;
    }

    /**
     * @param Money $price
     */
    private function validatePrice(Money $price): void
    {
        if ($price->getAmount() < 0) {
            throw InvalidPriceException::create();
        }
    }

    /**
     * @param string $name
     */
    private function validateName(string $name): void
    {
        if (empty($name)) {
            throw InvalidNameException::create();
        }
    }
}
