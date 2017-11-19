<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserManager;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Product;
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
        $fallout = new Product(Uuid::uuid4(), 'Fallout', Money::USD(199));
        $manager->persist($fallout);
        $dontStarve = new Product(Uuid::uuid4(), 'Don’t Starve', Money::USD(299));
        $manager->persist($dontStarve);
        $baldur = new Product(Uuid::uuid4(), 'Baldur’s Gate', Money::USD(399));
        $manager->persist($baldur);
        $icewind = new Product(Uuid::uuid4(), 'Icewind Dale', Money::USD(499));
        $manager->persist($icewind);
        $blood = new Product(Uuid::uuid4(), 'Bloodborne', Money::USD(599));
        $manager->persist($blood);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
