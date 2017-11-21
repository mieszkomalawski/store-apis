<?php
declare(strict_types=1);

namespace Store\Checkout\Exception;


use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class CannotRemoveProductException extends \LogicException
{
    /**
     * @param Uuid $productId
     * @return CannotRemoveProductException
     */
    static public function create(UuidInterface $productId): CannotRemoveProductException
    {
        return new self('Cannot remove product by id ' . $productId->toString() . ', product not found');
    }
}