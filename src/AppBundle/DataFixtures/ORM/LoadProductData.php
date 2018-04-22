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
use Store\SharedKernel\MoneyFactory;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProductData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        //hard coded for test purposes
        $fallout = new Product(Uuid::fromString('162e2dc2-6761-4a4e-9203-05f367d7ccd9'), 'Fallout', MoneyFactory::USD(1.99));
        $fallout->setPriority(3);
        $manager->persist($fallout);
        $dontStarve = new Product(Uuid::fromString('0d6f44d0-ad57-475d-876a-d20180fc22bd'), 'Don’t Starve', MoneyFactory::USD(2.99));
        $dontStarve->setPriority(1);
        $manager->persist($dontStarve);
        $blood = new Product(Uuid::fromString('4d690f62-8d63-4b53-be57-ab6e5381772b'), 'Bloodborne', MoneyFactory::USD(5.99));
        $blood->setPriority(2);
        $manager->persist($blood);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
