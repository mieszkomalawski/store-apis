<?php


namespace AppBundle\Controller;

use AppBundle\Command\CreateProductCommand;
use AppBundle\Command\UpdateProductCommand;
use AppBundle\Form\CreateProductType;
use AppBundle\Form\UpdateProductType;
use AppBundle\Repository\ProductRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\FOSRestController;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Store\Catalog\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;


class ExceptionController extends FOSRestController
{
    public function showAction()
    {
        return new JsonResponse([]);
   }
}
