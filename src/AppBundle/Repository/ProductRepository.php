<?php


namespace AppBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\UuidInterface;
use Store\Catalog\Product;
use Store\Checkout\AvailableProductCollection;

class ProductRepository extends EntityRepository implements AvailableProductCollection
{
    const PRODUCT_PER_PAGE = 3;

    public function getList(int $page)
    {
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $start = self::PRODUCT_PER_PAGE * ($page - 1);
        $stop = self::PRODUCT_PER_PAGE + 1;
        $dql = "SELECT p FROM Store\Catalog\Product p";
        $query = $em->createQuery($dql)->setFirstResult($start)->setMaxResults($stop);
        $products = $query->execute();

        if (count($products) < self::PRODUCT_PER_PAGE + 1) {
            $hasNextPage = false;
        } else {
            $hasNextPage = true;
            array_pop($products);
        }

        return new ProductPagination($hasNextPage, $products);
    }

    /**
     * @param UuidInterface $id
     * @return Product
     */
    public function getById(UuidInterface $id): Product
    {
        return $this->find($id);
    }
}
