<?php


namespace AppBundle\Controller\Checkout;

use AppBundle\Command\CreateProductCommand;
use AppBundle\Command\UpdateProductCommand;
use AppBundle\Form\AddProductToCartType;
use AppBundle\Form\CreateProductType;
use AppBundle\Form\UpdateProductType;
use AppBundle\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use FOS\RestBundle\Controller\FOSRestController;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Store\Catalog\Product;
use Store\Checkout\Cart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Request $request
     * @return Response
     */
    public function getCartAction(Cart $cart)
    {
        $view = $this->view([
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