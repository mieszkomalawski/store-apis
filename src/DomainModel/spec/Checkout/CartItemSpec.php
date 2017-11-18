<?php

namespace spec\Store\Checkout;

use Money\Money;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Product;
use Store\Checkout\CartItem;
use PhpSpec\ObjectBehavior;

class CartItemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            Money::PLN(100)
        );
        $this->beConstructedWith($product, 1);
        $this->shouldHaveType(CartItem::class);
    }

    public function it_should_not_have_negative_quantity()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            Money::PLN(100)
        );
        $this->shouldThrow(new \InvalidArgumentException('Cannot have negative amount of item'))->during('__construct', [
            $product,
            -1
        ]);
    }
}
