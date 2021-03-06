<?php


namespace AppBundle\Controller\Catalog;

use AppBundle\Model\CreateProductCommand;
use AppBundle\Model\UpdateProductCommand;
use AppBundle\Form\CreateProductType;
use AppBundle\Form\UpdateProductType;
use AppBundle\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\FOSRestController;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Store\Catalog\Product;
use Store\Catalog\ProductCategory;
use Store\SharedKernel\MoneyFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

/**
 * @SWG\Swagger(
 *     schemes={"http"},
 *     basePath="/api"
 * )
 * @SWG\Info(
 *     title="some api",
 *     version="0.1"
 * )
 * Class ProductController
 * @package AppBundle\Controller\Catalog
 */
class ProductController extends FOSRestController
{
    /**
     * @SWG\Get(
     *     path="/catalog/products",
     *     produces={"application/json"},
     *
     *  @SWG\Response(
     *     response=200,
     *     description="Returns list of products",
     *     @SWG\Schema(
     *         @SWG\Property(
     *           property="next",
     *           type="string",
     *           description="reference to next page of results"
     *         ),
     *         @SWG\Property(
     *           property="prev",
     *           type="string",
     *           description="reference to previous page of results"
     *         ),
     *         @SWG\Property(
     *           property="data",
     *           type="array",
     *           description="products list",
     *           @SWG\Items(ref="#/definitions/ProductListItem")
     *         )
     *     )
     * )
     * )
     *
     * @SWG\Definition(
     *     type="object",
     *     required={"id", "name", "price"},
     *     definition="ProductListItem",
     *     @SWG\Property(
     *        property="id",
     *        type="string",
     *        example="66e3a13c-d1a7-4bc0-9732-b99c184602e7"
     *     ),
     *     @SWG\Property(
     *        property="name",
     *        type="string",
     *        example="foo"
     *     ),
     *     @SWG\Property(
     *        property="price",
     *        type="number",
     *        example="9.99"
     *     )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function getProductsAction(Request $request)
    {
        $page = $request->get('page', 1);
        /** @var ProductRepository $productRepository */
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->getList($page);

        $view = $this->view([
            'data' => $products->getItems(),
            'next' => $products->isHasNextPage() ? $this->generateUrl('get_products', ['page' => $page + 1]) : '',
            'prev' => (($page > 1) ? $this->generateUrl('get_products', ['page' => $page - 1]) : ''),
        ], 200);

        return $this->handleView($view);
    }

    public function getCategoriesAction(Request $request)
    {
        $productCategoryRepository = $this->getDoctrine()->getRepository(ProductCategory::class);
        $categories = $productCategoryRepository->findAll();
        $view = $this->view([
            'data' => $categories,
            'next' => '',
            'prev' => '',
        ], 200);

        return $this->handleView($view);
    }


    /**
     * @Get("/products/{productId}", name="get_product", options={ "method_prefix" = false }, requirements={"productId" = "[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"})
     * @ParamConverter("product", options={"mapping": {"productId" : "id"}})
     * @SWG\Get(
     *     path="/catalog/products/{productId}",
     *     produces={"application/json"},
     *  @SWG\Parameter(
     *     in="path",
     *     enum={"162e2dc2-6761-4a4e-9203-05f367d7ccd9"},
     *     name="productId",
     *     required=true,
     *     type="string"
     *  ),
     *  @SWG\Response(
     *     response=200,
     *     description="Returns product",
     *     @SWG\Schema(
     *         ref="#/definitions/ProductListItem"
     *     )
     *  )
     * )
     *
     *
     * @param string $id
     * @return Response
     */
    public function getProductAction(Product $product)
    {
        $view = $this->view([
            'id' => $product->getId()->toString(),
            'name' => $product->getName(),
            // amount is in lowest units always
            'price' => $product->getPriceDecimal()
        ], 200);

        return $this->handleView($view);
    }

    /**
     *
     *  @SWG\Post(
     *     path="/catalog/products",
     *     produces={"application/json"},
     *     consumes={"application/json"},
     *  @SWG\Parameter(
     *     in="body",
     *     name="product",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/NewProduct")
     * ),
     *  @SWG\Response(
     *     response=201,
     *     description="Product created, url to created product representation in Location header",
     *     @SWG\Schema(
     *         type="array",
     *          @SWG\Items()
     *     )
     * )
     * )
     *
     * @SWG\Definition(
     *     type="object",
     *     required={"name", "price"},
     *     definition="NewProduct",
     *     @SWG\Property(
     *        property="name",
     *        type="string",
     *        example="foo",
     *        minLength=1,
     *     ),
     *     @SWG\Property(
     *        property="price",
     *        type="number",
     *        example="9.99",
     *        format="decimal",
     *        minimum=0.01
     *     )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function postProductAction(Request $request)
    {
        $form = $this->createForm(CreateProductType::class, new CreateProductCommand());

        $form->submit($request->request->all());

        if ($form->isValid()) {
            /** @var CreateProductCommand $createProductCommand */
            $createProductCommand = $form->getData();
            $uuid = Uuid::uuid4();
            $product = new Product(
                $uuid,
                $createProductCommand->getName(),
                MoneyFactory::USD($createProductCommand->getPrice())
            );
            $this->getDoctrine()->getManager()->persist($product);
            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse(
                [],
                201,
                ['Location' => $this->generateUrl('get_product', ['productId' => $product->getId()])]
            );
        }

        $view = $this->view($form);

        return $this->handleView($view);
    }

    /**
     *
     * @Put("/products/{productId}", name="put_product", options={ "method_prefix" = false }, requirements={"productId" = "[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"})
     * @ParamConverter("product", options={"mapping": {"productId" : "id"}})
     * @SWG\Put(
     *     path="/catalog/products/{productId}",
     *     produces={"application/json"},
     *     consumes={"application/json"},
     *  @SWG\Parameter(
     *     in="path",
     *     enum={"162e2dc2-6761-4a4e-9203-05f367d7ccd9"},
     *     name="productId",
     *     required=true,
     *     type="string"
     * ),
     *  @SWG\Parameter(
     *     in="body",
     *     name="product",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/UpdateProduct")
     * ),
     *  @SWG\Response(
     *     response=200,
     *     description="Product updated, url to updated product representation in Location header",
     *     @SWG\Schema(
     *         type="array",
     *          @SWG\Items()
     *     )
     * )
     * )
     *
     * @SWG\Definition(
     *     type="object",
     *     definition="UpdateProduct",
     *     @SWG\Property(
     *        property="name",
     *        minLength=1,
     *        type="string",
     *        example="foo"
     *     ),
     *     @SWG\Property(
     *        property="price",
     *        type="number",
     *        example="9.99",
     *        format="decimal",
     *        minimum=0.01
     *     )
     * )
     *
     * @param Product $product
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function putProductAction(Product $product, Request $request)
    {
        /**
         * I'm using command object to bypass symfony validation philosophy,
         * I prefer to have always valid entities
         */
        $updateProductCommand = new UpdateProductCommand();
        $updateProductCommand->setPrice($product->getPriceDecimal());
        $updateProductCommand->setName($product->getName());
        $form = $this->createForm(UpdateProductType::class, $updateProductCommand);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            /** @var UpdateProductCommand $updateProductCommand */
            $updateProductCommand = $form->getData();
            if ($updateProductCommand->getName()) {
                $product->changeName($updateProductCommand->getName());
            }
            if ($updateProductCommand->getPrice()) {
                $product->changePrice(MoneyFactory::USD($updateProductCommand->getPrice()));
            }
            $this->getDoctrine()->getManager()->persist($product);
            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse(
                [],
                200,
                ['Location' => $this->generateUrl('get_product', ['productId' => $product->getId()])]
            );
        }

        $view = $this->view($form);

        return $this->handleView($view);
    }

    /**
     * @Delete("/products/{productId}", name="delete_product", options={ "method_prefix" = false }, requirements={"productId" = "[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}"})
     * @ParamConverter("product", options={"mapping": {"productId" : "id"}})
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function deleteProductAction(Product $product)
    {
        $this->getDoctrine()->getManager()->remove($product);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([], 200);
    }
}
