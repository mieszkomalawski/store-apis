<?php

namespace spec\Store\Checkout;

use Doctrine\Common\Collections\ArrayCollection;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Product;
use Store\Checkout\Cart;
use PhpSpec\ObjectBehavior;
use Store\Checkout\CartItem;

class CartSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(Uuid::uuid4());
    }
    function it_is_initializable()
    {
        $this->shouldHaveType(Cart::class);
    }

    public function it_should_add_product_to_cart()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            Money::USD(100)
        );
        $this->add($product, 1);
    }

    public function it_should_increase_quantity_when_adding_same_product()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            Money::USD(100)
        );
        $this->add($product, 1);
        $this->add($product, 1);

        $products = $this->getProducts();
        $products->count()->shouldBeLike(1);
        $products->first()->getName()->shouldBeLike('testProduct');
    }

    public function it_should_remove_product()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            Money::USD(100)
        );
        $this->add($product, 1);
        $this->remove($product->getId());

        $this->getProducts()->shouldBeLike(new ArrayCollection());
    }

    public function it_should_throw_exception_when_product_not_found()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            Money::USD(100)
        );
        $this->add($product, 1);
        $uuid = Uuid::uuid4();

        $this->shouldThrow(new \InvalidArgumentException('Cannot remove product by id '. (string)$uuid . ', product not found'))
            ->during('remove', [$uuid]);
    }

    public function it_should_not_have_more_than_3_products()
    {
        $product1 = new Product(
            Uuid::uuid4(),
            'testProduct',
            Money::USD(100)
        );
        $this->add($product1, 1);
        $product2 = new Product(
            Uuid::uuid4(),
            'testProduct2',
            Money::USD(200)
        );
        $this->add($product2, 1);
        $product3 = new Product(
            Uuid::uuid4(),
            'testProduct3',
            Money::USD(100)
        );
        $this->add($product3, 1);

        $product4 = new Product(
            Uuid::uuid4(),
            'testProduct4',
            Money::USD(300)
        );

        $this->shouldThrow(new \InvalidArgumentException('Cannot have more than 3 products in cart'))
            ->during('add', [$product4, 1]);
    }

    public function it_should_return_total_price()
    {
        $product1 = new Product(
            Uuid::uuid4(),
            'testProduct',
            Money::USD(99,99)
        );
        $this->add($product1, 1);
        $product2 = new Product(
            Uuid::uuid4(),
            'testProduct2',
            Money::USD(220)
        );
        $this->add($product2, 2);

        $this->getTotal()->shouldBeLike(Money::USD(440 + 99,99));
    }
}
