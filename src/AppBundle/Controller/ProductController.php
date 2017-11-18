<?php


namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Store\Catalog\Product;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends FOSRestController
{
    public function getProductsAction(Request $request)
    {
        // we dont need pagination wrapper in rest api
        $items = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $view = $this->view($items, 200);

        return $this->handleView($view);
    }
}