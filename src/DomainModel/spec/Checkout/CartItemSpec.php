<?php

namespace spec\Store\Checkout;

use Money\Money;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Product;
use Store\Checkout\Cart;
use Store\Checkout\CartItem;
use PhpSpec\ObjectBehavior;

class CartItemSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            Money::USD(100)
        );
        $cart = new Cart(Uuid::uuid4());
        $this->beConstructedWith(Uuid::uuid4(), $cart, $product, 1);
        $this->shouldHaveType(CartItem::class);
    }

    public function it_should_not_have_negative_quantity()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            Money::USD(100)
        );
        $cart = new Cart(Uuid::uuid4());
        $this->shouldThrow(new \InvalidArgumentException('Cannot have negative amount of item'))->during('__construct', [
            Uuid::uuid4(),
            $cart,
            $product,
            -1
        ]);
    }
}
