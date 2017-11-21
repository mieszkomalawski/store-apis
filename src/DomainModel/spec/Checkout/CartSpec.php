<?php

namespace spec\Store\Checkout;

use Doctrine\Common\Collections\ArrayCollection;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Product;
use Store\Checkout\Cart;
use PhpSpec\ObjectBehavior;
use Store\Checkout\CartItem;
use Store\Checkout\Exception\CannotRemoveProductException;
use Store\Checkout\Exception\TooManyProductsException;
use Store\Checkout\InMemoryAvailableProductCollection;
use Store\SharedKernel\MoneyFactory;

class CartSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedThrough('create', [Uuid::uuid4()]);
    }
    public function it_is_initializable()
    {
        $this->shouldHaveType(Cart::class);
    }

    public function it_should_add_product_to_cart()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            MoneyFactory::USD(100)
        );
        $this->add($product->getId());
    }

    public function it_should_remove_product()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            MoneyFactory::USD(100)
        );
        $this->add($product->getId());
        $this->remove($product->getId());

        $this->getProducts()->shouldBeLike(new ArrayCollection());
    }

    public function it_should_throw_exception_when_product_not_found()
    {
        $product = new Product(
            Uuid::uuid4(),
            'testProduct',
            MoneyFactory::USD(100)
        );
        $this->add($product->getId());
        $uuid = Uuid::uuid4();

        $this->shouldThrow(new CannotRemoveProductException('Cannot remove product by id '. (string)$uuid . ', product not found'))
            ->during('remove', [$uuid]);
    }

    public function it_should_not_have_more_than_3_products()
    {
        $product1 = new Product(
            Uuid::uuid4(),
            'testProduct',
            MoneyFactory::USD(100)
        );
        $this->add($product1->getId());
        $product2 = new Product(
            Uuid::uuid4(),
            'testProduct2',
            MoneyFactory::USD(200)
        );
        $this->add($product2->getId());
        $product3 = new Product(
            Uuid::uuid4(),
            'testProduct3',
            MoneyFactory::USD(100)
        );
        $this->add($product3->getId());

        $product4 = new Product(
            Uuid::uuid4(),
            'testProduct4',
            MoneyFactory::USD(300)
        );

        $this->shouldThrow(new TooManyProductsException('Cannot have more than 3 products in cart'))
            ->during('add', [$product4->getId()]);
    }

    public function it_should_return_total_price()
    {
        $product1 = new Product(
            Uuid::uuid4(),
            'testProduct',
            MoneyFactory::USD(99.99)
        );
        $this->add($product1->getId());
        $product2 = new Product(
            Uuid::uuid4(),
            'testProduct2',
            MoneyFactory::USD(220)
        );
        $this->add($product2->getId());

        $this->getTotal(new InMemoryAvailableProductCollection([$product1, $product2]))->shouldBeLike(MoneyFactory::USD(220 + 99.99));
    }
}
