<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserManager;
use Money\Money;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Product;
use Store\Checkout\Cart;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCartData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * @var Container
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        // hard coded uuid for test purposes
        $cart = Cart::create(Uuid::fromString('3d73fbef-7998-4836-a521-004fdfbb0241'));
        $cart->add(Uuid::fromString('162e2dc2-6761-4a4e-9203-05f367d7ccd9'));

        /** @var AggregateRepository $cartAggregateRepository */
        $cartAggregateRepository = $this->container->get('cart_aggregate_repository');
        $cartAggregateRepository->saveAggregateRoot($cart);

    }

    public function getOrder()
    {
        return 2;
    }
}
