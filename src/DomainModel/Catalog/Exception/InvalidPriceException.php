<?php


namespace Store\Catalog\Exception;


class InvalidPriceException extends \InvalidArgumentException
{
    /**
     * @return InvalidPriceException
     */
    static public function create(): InvalidPriceException
    {
        return new self('Product price cannot be negative');
    }
}