<?php


namespace AppBundle\Serializer;


use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Store\Catalog\Product;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ProductHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => Product::class,
                'method' => 'serializeProduct',
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
    public function serializeProduct(JsonSerializationVisitor $visitor, Product $product, array $type, Context $context)
    {
        return [
            'id' => $product->getId()->toString(),
            'name' => $product->getName(),
            // amount is in lowest units always
            'price' => $product->getPrice()->getAmount() / 100
        ];
    }

}