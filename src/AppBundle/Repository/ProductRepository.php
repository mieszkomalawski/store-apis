<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
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
}