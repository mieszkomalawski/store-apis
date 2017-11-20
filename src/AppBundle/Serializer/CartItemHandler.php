<?php


namespace AppBundle\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;
use Store\Catalog\Product;
use Store\Checkout\CartItem;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CartItemHandler implements SubscribingHandlerInterface
{
    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => CartItem::class,
                'method' => 'serializeCartItem',
            )
        );
    }

    /**
     * @param JsonSerializationVisitor $visitor
     * @param Product $cartItem
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializeCartItem(JsonSerializationVisitor $visitor, CartItem $cartItem, array $type, Context $context)
    {
        return [
            'id' => $cartItem->getProductId()->toString(),
            'name' => $cartItem->getName(),
            'quantity' => $cartItem->getQuantity(),
            // amount is in lowest units always
            'price' => $cartItem->getPrice()->getAmount() / 100
        ];
    }
}
