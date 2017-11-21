<?php


namespace Store\Catalog\Exception;


class InvalidNameException extends \InvalidArgumentException
{
    /**
     * @return InvalidPriceException
     */
    static public function create(): InvalidNameException
    {
        return new self('Name cannot be blank');
    }
}