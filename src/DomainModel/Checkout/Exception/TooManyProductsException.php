<?php
declare(strict_types=1);

namespace Store\Checkout\Exception;


class TooManyProductsException extends \InvalidArgumentException
{
    /**
     * @return TooManyProductsException
     */
    public static function create(): TooManyProductsException
    {
        return new self('Cannot have more than 3 products in cart');
    }
}