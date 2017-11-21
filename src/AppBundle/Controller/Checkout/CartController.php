<?php


namespace AppBundle\Controller\Checkout;

use AppBundle\Form\AddProductToCartType;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Store\Catalog\Product;
use Store\Checkout\Cart;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class CartController extends FOSRestController
{
    /**
     * @var AggregateRepository
     */
    private $cartAggregateRepository;

    /**
     * CartController constructor.
     */
    public function __construct(AggregateRepository $cartAggregateRepository)
    {
        $this->cartAggregateRepository = $cartAggregateRepository;
    }


    /**
     *
     * @SWG\Post(
     *     path="/checkout/carts",
     *     produces={"application/json"},
     *     consumes={"application/json"},
     *  @SWG\Response(
     *     response=201,
     *     description="Cart created",
     *     @SWG\Schema(
     *         type="array",
     *          @SWG\Items()
     *     )
     * )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function postCartAction(Request $request)
    {
        $id = Uuid::uuid4();
        $cart = Cart::create(
            $id,
            $this->getDoctrine()->getRepository(Product::class)
        );
        $this->cartAggregateRepository->saveAggregateRoot($cart);

        return new JsonResponse(
            [],
            201,
            ['Location' => $this->generateUrl('get_cart', ['cart' => $id])]
        );
    }

    /**
     * @Get("/carts/{cart}", name="get_cart", options={ "method_prefix" = false }, requirements={"cart" = "[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"})
     *
     * @SWG\Get(
     *     path="/checkout/carts/{cartId}",
     *     produces={"application/json"},
     *     consumes={"application/json"},
     *       @SWG\Parameter(
     *          in="path",
     *          enum={"3d73fbef-7998-4836-a521-004fdfbb0241"},
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
     *                  minimum=0
     *              )
     * )
     *
     * @SWG\Definition(
     *     type="object",
     *     required={"id", "name", "price"},
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
     *               @SWG\Property(
     *                  property="price",
     *                  type="number",
     *                  example="9.99",
     *                   minimum=0.01
     *              )
     * )
     *
     * @param string $cart
     * @return Response
     */
    public function getCartAction($cart)
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);

        /** @var Cart $cart */
        $cartAggregate = $this->cartAggregateRepository->getAggregateRoot((string)$cart);
        if (!$cartAggregate instanceof Cart) {
            return new JsonResponse(['message' => 'Cart not found by id: ' . $cart], 404);
        }
        $products = $cartAggregate->getProducts()->map(
            function (UuidInterface $productId) use ($productRepository) {
                return $productRepository->find($productId);
            }
        );
        $view = $this->view([
            'id' => $cartAggregate->getId()->toString(),
            'products' => array_values($products->toArray()),
            'total' => $cartAggregate->getTotal($productRepository)->getAmount() / 100
        ], 200);

        return $this->handleView($view);
    }

    /**
     * @Post("/carts/{cart}/products", name="post_cart_product", options={ "method_prefix" = false }, requirements={"cart" = "[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"})
     *
     * @SWG\Post(
     *     path="/checkout/carts/{cartId}/products",
     *     produces={"application/json"},
     *     consumes={"application/json"},
     *       @SWG\Parameter(
     *          in="path",
     *          enum={"3d73fbef-7998-4836-a521-004fdfbb0241"},
     *          name="cartId",
     *          required=true,
     *          type="string"
     *      ),
     *  @SWG\Parameter(
     *     in="body",
     *     name="addProductToCart",
     *     required=true,
     *     @SWG\Schema(
     *         ref="#/definitions/AddProductToCart"
     *     )
     * ),
     *  @SWG\Response(
     *     response=201,
     *     description="Product added to cart",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items()
     *     )
     *  )
     * )
     *
     * @SWG\Definition(
     *     type="object",
     *     required={"product"},
     *     definition="AddProductToCart",
     *
     *               @SWG\Property(
     *                  property="product",
     *                  type="string",
     *                  example="7dbaf7f6-c415-42cf-85c2-9a8fababcba6"
     *              )
     *
     *
     * )
     *
     * @param string $cart
     * @param Request $request
     * @return Response
     */
    public function postCartProductAction($cart, Request $request)
    {
        $cartAggregate = $this->cartAggregateRepository->getAggregateRoot($cart);
        if (!$cartAggregate instanceof Cart) {
            return new JsonResponse(['message' => 'Cart not found by id: ' . $cart], 404);
        }

        $form = $this->createForm(AddProductToCartType::class);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();
            /** @var Product $product */
            $product = $data['product'];
            $cartAggregate->add($product->getId());
            $this->cartAggregateRepository->saveAggregateRoot($cartAggregate);

            return new JsonResponse(
                [],
                201,
                ['Location' => $this->generateUrl('get_cart', ['cart' => $cartAggregate->getId()])]
            );
        }

        $view = $this->view($form);

        return $this->handleView($view);
    }

    /**
     * @Delete("/carts/{cart}/products/{product}", name="delete_cart_product", options={ "method_prefix" = false }, requirements={"cart" = "[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}", "product" = "[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"})
     *
     * @SWG\Delete(
     *     path="/checkout/carts/{cartId}/products/{productId}",
     *     produces={"application/json"},
     *     consumes={"application/json"},
     *       @SWG\Parameter(
     *          in="path",
     *          enum={"3d73fbef-7998-4836-a521-004fdfbb0241"},
     *          name="cartId",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          in="path",
     *          enum={"162e2dc2-6761-4a4e-9203-05f367d7ccd9"},
     *          name="productId",
     *          required=true,
     *          type="string"
     *      ),
     *  @SWG\Response(
     *     response=200,
     *     description="Product removed from cart",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items()
     *     )
     *  )
     * )
     *
     * @param string $cart
     * @param Product $product
     * @return Response
     */
    public function deleteCartProductAction($cart, Product $product)
    {
        /** @var Cart $cart */
        $cartAggregate = $this->cartAggregateRepository->getAggregateRoot((string)$cart);
        if (!$cartAggregate instanceof Cart) {
            return new JsonResponse(['message' => 'Cart not found by id: ' . $cart], 404);
        }

        $cartAggregate->remove($product->getId());
        $this->cartAggregateRepository->saveAggregateRoot($cartAggregate);

        return new JsonResponse([], 200);
    }
}
