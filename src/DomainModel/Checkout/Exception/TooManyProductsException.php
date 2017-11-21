<?php


namespace Store\Checkout\Exception;


class TooManyProductsException extends \InvalidArgumentException
{
    /**
     * @return TooManyProductsException
     */
    public static function create(): TooManyProductsException
    {
        return new TooManyProductsException('Cannot have more than 3 products in cart');
    }
}