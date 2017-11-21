<?php


namespace Store\SharedKernel;


use Money\Currency;
use Money\Money;

class MoneyFactory
{
    /**
     * Money class uses smallest unit so 599 = 5 dollers 99 cents
     * This class converts it
     * @param $method
     * @param $arguments
     * @return Money
     */
    public static function __callStatic($method, $arguments): Money
    {
        return new Money($arguments[0] * 100, new Currency($method));
    }
}