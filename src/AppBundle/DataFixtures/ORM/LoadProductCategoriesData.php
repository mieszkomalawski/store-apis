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
use Store\Catalog\ProductCategory;
use Store\Checkout\Cart;
use Store\SharedKernel\MoneyFactory;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProductCategoriesData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        $shooters = new ProductCategory(Uuid::fromString('162e2dc2-6761-4a4e-9203-05f367d7ccd9'), 'Shooters');
        $manager->persist($shooters);
        $rts = new ProductCategory(Uuid::fromString('4d690f62-8d63-4b53-be57-ab6e5381772b'), 'RTS');
        $manager->persist($rts);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
