<?php


namespace AppBundle\Controller\Catalog;

use AppBundle\Command\CreateProductCommand;
use AppBundle\Command\UpdateProductCommand;
use AppBundle\Form\CreateProductType;
use AppBundle\Form\UpdateProductType;
use AppBundle\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends FOSRestController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function getProductsAction(Request $request)
    {
        $page = $request->get('page', 1);
        /** @var ProductRepository $productRepository */
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $products = $productRepository->getList($page);

        // we dont need pagination wrapper in rest api
        //$items = $this->getProductRepository()->findAll();
        $view = $this->view([
            'data' => $products->getItems(),
            'next' => $products->isHasNextPage() ? $this->generateUrl('get_products', ['page' => $page + 1]) : null,
            'prev' => (($page > 1) ? $this->generateUrl('get_products', ['page' => $page - 1]) : null),
        ], 200);

        return $this->handleView($view);
    }

    public function getProductAction(Product $product)
    {
        $view = $this->view($product, 200);

        return $this->handleView($view);
    }

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
                Money::USD($createProductCommand->getPrice() * 100)
            );
            $this->getDoctrine()->getManager()->persist($product);
            $this->getDoctrine()->getManager()->flush();

            return new Response('', 201,
                ['Location' => $this->generateUrl('get_product', ['product' => $product->getId()])]);
        }

        $view = $this->view($form);

        return $this->handleView($view);
    }

    public function putProductAction(Product $product, Request $request)
    {
        $form = $this->createForm(UpdateProductType::class, new UpdateProductCommand());

        $form->submit($request->request->all());

        if ($form->isValid()) {
            /** @var UpdateProductCommand $updateProductCommand */
            $updateProductCommand = $form->getData();
            if ($updateProductCommand->getName()) {
                $product->changeName($updateProductCommand->getName());
            }
            if ($updateProductCommand->getPrice()) {
                $product->changePrice(Money::USD($updateProductCommand->getPrice() * 100));
            }
            $this->getDoctrine()->getManager()->persist($product);
            $this->getDoctrine()->getManager()->flush();

            return new Response('', 200,
                ['Location' => $this->generateUrl('get_product', ['product' => $product->getId()])]);
        }

        $view = $this->view($form);

        return $this->handleView($view);
    }

    public function deleteProductAction(Product $product)
    {
        $this->getDoctrine()->getManager()->remove($product);
        $this->getDoctrine()->getManager()->flush();

        return new Response('', 200);
    }

    /**
     * @return ObjectRepository
     */
    private function getProductRepository(): ObjectRepository
    {
        return $this->getDoctrine()->getRepository(Product::class);
    }
}