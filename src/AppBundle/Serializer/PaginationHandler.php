<?php


namespace AppBundle\Serializer;


use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use Store\Catalog\Product;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class PaginationHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => Paginator::class,
                'method' => 'serializePaginator',
            )
        );
    }


    /**
     * @param JsonSerializationVisitor $visitor
     * @param Product $product
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializePaginator(JsonSerializationVisitor $visitor, PaginationInterface $paginator, array $type, Context $context)
    {
        return [
            'items' => $paginator->getItems()
        ];
    }

}