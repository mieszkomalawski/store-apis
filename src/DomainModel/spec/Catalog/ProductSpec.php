<?php

namespace spec\Store\Catalog;

use Money\Money;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Product;
use PhpSpec\ObjectBehavior;

class ProductSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(
            Uuid::uuid4(),
            'testProduct',
            Money::PLN(100)
        );
        $this->shouldHaveType(Product::class);
    }

    public function it_should_not_accept_negative_price()
    {
        $this->shouldThrow(new \InvalidArgumentException('Product price cannot be negative'))->during('__construct', [
            Uuid::uuid4(),
            'testProduct',
            Money::PLN(-100)
        ]);
    }

    public function it_should_not_have_empty_name()
    {
        $this->shouldThrow(new \InvalidArgumentException('Name cannot be blank'))->during('__construct', [
            Uuid::uuid4(),
            '',
            Money::PLN(100)
        ]);
    }
}
