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
        $manager->persist($fallout);
        $dontStarve = new Product(Uuid::uuid4(), 'Don’t Starve', MoneyFactory::USD(2.99));
        $manager->persist($dontStarve);
        $baldur = new Product(Uuid::uuid4(), 'Baldur’s Gate', MoneyFactory::USD(3.99));
        $manager->persist($baldur);
        $icewind = new Product(Uuid::uuid4(), 'Icewind Dale', MoneyFactory::USD(4.99));
        $manager->persist($icewind);
        $blood = new Product(Uuid::uuid4(), 'Bloodborne', MoneyFactory::USD(5.99));
        $manager->persist($blood);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
