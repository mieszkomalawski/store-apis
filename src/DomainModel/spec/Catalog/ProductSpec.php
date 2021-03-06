<?php

namespace spec\Store\Catalog;

use Money\Money;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Exception\InvalidNameException;
use Store\Catalog\Exception\InvalidPriceException;
use Store\Catalog\Product;
use PhpSpec\ObjectBehavior;

class ProductSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith(
            Uuid::uuid4(),
            'testProduct',
            Money::USD(100)
        );
        $this->shouldHaveType(Product::class);
    }

    public function it_should_not_accept_negative_price()
    {
        $this->shouldThrow(new InvalidPriceException('Product price cannot be negative'))->during('__construct', [
            Uuid::uuid4(),
            'testProduct',
            Money::USD(-100)
        ]);
    }

    public function it_should_not_have_empty_name()
    {
        $this->shouldThrow(new InvalidNameException('Name cannot be blank'))->during('__construct', [
            Uuid::uuid4(),
            '',
            Money::USD(100)
        ]);
    }
}
