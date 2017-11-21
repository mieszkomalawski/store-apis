<?php


namespace AppBundle\Controller;

use AppBundle\Model\CreateProductCommand;
use AppBundle\Model\UpdateProductCommand;
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
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class ExceptionController extends FOSRestController
{
    /**
     * @param \Exception $exception
     * @return JsonResponse
     */
    public function showAction(\Exception $exception)
    {
        if ($this->isDebugMode()) {
            $responseBody['exception'] = $exception->getMessage();
            $responseBody['class'] = get_class($exception);
        }
        if ($exception instanceof NotFoundHttpException) {
            $responseBody['message'] = 'route not found';
            return new JsonResponse($responseBody, 404);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            $responseBody['message'] = 'method not allowed';
            return new JsonResponse($responseBody, 405);
        }

        $responseBody['message'] = 'unknown error, please contact support';
        return new JsonResponse($responseBody, 500);
    }

    public function isDebugMode()
    {
        return $this->getParameter('kernel.debug');
    }
}
