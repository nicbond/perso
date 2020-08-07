<?php

namespace App\Controller;

use App\Entity\Shop;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Service\Call;

class ShopController extends AbstractFOSRestController
{
    /**
     * @Rest\Get("les-habitues/shops", name="app_shops_list")
     * @param Request $request
     * @return Response
     */
    public function shop(Request $request, Call $call): Response
    {
        $fisrt = $call->getConnexion();
        return $fisrt;
    }

    /**
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "les-habitues/shops/{id}",
     *     name = "app_shops_delete",
     *     requirements = {"id"="\d+"}
     * )
     */
    public function delete(Shop $shop)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($shop);
        $em->flush();

        return;
    }
}
