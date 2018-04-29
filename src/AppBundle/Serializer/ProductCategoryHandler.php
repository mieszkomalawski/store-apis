<?php


namespace AppBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Store\Catalog\Product;
use Store\Catalog\ProductCategory;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ProductCategoryHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => ProductCategory::class,
                'method' => 'serializeCategory',
            )
        );
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param Product $category
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializeCategory(JsonSerializationVisitor $visitor, ProductCategory $category, array $type, Context $context)
    {
        return [
            'id' => $category->getId()->toString(),
            'name' => $category->getName()
        ];
    }
}
