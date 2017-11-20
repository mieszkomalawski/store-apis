<?php


namespace AppBundle\Controller\Checkout;

use AppBundle\Form\AddProductToCartType;
use FOS\RestBundle\Controller\FOSRestController;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Product;
use Store\Checkout\Cart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class CartController extends FOSRestController
{
    public function postCartAction(Request $request)
    {
        $id = Uuid::uuid4();
        $cart = new Cart($id);

        $this->getDoctrine()->getManager()->persist($cart);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', 201,
                ['Location' => $this->generateUrl('get_cart', ['cart' => $id])]);

    }

    /**
     *
     *  @SWG\Get(
     *     path="/checkout/carts/{cartId}",
     *     produces={"application/json"},
     *     consumes={"application/json"},
     *       @SWG\Parameter(
     *          in="path",
     *          enum={"0ecdc635-bb14-4ffa-8826-756d9cc3c73d"},
     *          name="cartId",
     *          required=true,
     *          type="string"
     *      ),
     *  @SWG\Response(
     *     response=200,
     *     description="Returns single cart with products",
     *     @SWG\Schema(
     *         ref="#/definitions/Cart"
     *     )
     *  )
     * )
     *
     * @SWG\Definition(
     *     type="object",
     *     required={"id", "products", "total"},
     *     definition="Cart",
     *
     *               @SWG\Property(
     *                  property="id",
     *                  type="string",
     *                  example="0ecdc635-bb14-4ffa-8826-756d9cc3c73d"
     *              ),
     *               @SWG\Property(
     *                  property="products",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/ProductCartItem")
     *              ),
     *               @SWG\Property(
     *                  property="total",
     *                  type="number",
     *                  example="9.99",
     *                  minimum=0.01
     *              )
     * )
     *
     * @SWG\Definition(
     *     type="object",
     *     required={"id", "name", "price", "quantity"},
     *     definition="ProductCartItem",
     *               @SWG\Property(
     *                  property="id",
     *                  type="string",
     *                  example="66e3a13c-d1a7-4bc0-9732-b99c184602e7"
     *              ),
     *               @SWG\Property(
     *                  property="name",
     *                  type="string",
     *                  example="foo"
     *              ),
     *              @SWG\Property(
     *                  property="quantity",
     *                  type="integer",
     *                  example="1",
     *                   minimum=1
     *              ),
     *               @SWG\Property(
     *                  property="price",
     *                  type="number",
     *                  example="9.99",
     *                   minimum=0.01
     *              )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function getCartAction(Cart $cart)
    {
        $view = $this->view([
            'id' => $cart->getId()->toString(),
            'products' => $cart->getProducts(),
            'total' => $cart->getTotal()->getAmount() / 100
        ], 200);

        return $this->handleView($view);
    }


    public function postCartProductAction(Cart $cart, Request $request)
    {
        $form = $this->createForm(AddProductToCartType::class);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();
            $cart->add($data['product'], $data['quantity']);
            $this->getDoctrine()->getManager()->persist($cart);
            $this->getDoctrine()->getManager()->flush();

            return new Response('', 201,
                ['Location' => $this->generateUrl('get_cart', ['cart' => $cart->getId()])]);
        }

        $view = $this->view($form);

        return $this->handleView($view);
    }

    public function deleteCartProductAction(Cart $cart, Product $product)
    {
        $cart->remove($product->getId());
        $this->getDoctrine()->getManager()->persist($cart);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', 200);
    }
}