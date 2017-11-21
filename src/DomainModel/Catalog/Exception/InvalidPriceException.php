<?php
declare(strict_types=1);

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