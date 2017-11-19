<?php


namespace AppBundle\Repository;


class ProductPagination
{
    /**
     * @var bool
     */
    private $hasNextPage;

    /**
     * @var iterable
     */
    private $items;

    /**
     * ProductPagination constructor.
     * @param bool $hasNextPage
     * @param iterable $items
     */
    public function __construct($hasNextPage, $items)
    {
        $this->hasNextPage = $hasNextPage;
        $this->items = $items;
    }

    /**
     * @return bool
     */
    public function isHasNextPage(): bool
    {
        return $this->hasNextPage;
    }

    /**
     * @return iterable
     */
    public function getItems(): iterable
    {
        return $this->items;
    }

}