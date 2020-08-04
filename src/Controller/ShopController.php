<?php

namespace App\Controller;

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
     */
    public function shop(Request $request, Call $call)
    {
        $fisrt = $call->getConnexion();
    }
}
