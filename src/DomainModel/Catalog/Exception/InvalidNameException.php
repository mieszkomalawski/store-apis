<?php
declare(strict_types=1);

namespace Store\Catalog\Exception;

class InvalidNameException extends \InvalidArgumentException
{
    /**
     * @return InvalidNameException
     */
    public static function create(): InvalidNameException
    {
        return new self('Name cannot be blank');
    }
}
