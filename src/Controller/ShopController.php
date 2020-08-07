<?php

namespace App\Controller;

use App\Entity\Shop;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use App\Exception\ResourceValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @Rest\View(StatusCode = 200)
     * @Rest\Put(
     *     path = "les-habitues/shops/{id}",
     *     name = "app_shops_update",
     *     requirements = {"id"="\d+"}
     * )
     * @ParamConverter("newShop", converter="fos_rest.request_body")
     */
    public function update(Shop $shop, Shop $newShop, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $shop->setNameShop($newShop->getNameShop());
        $shop->setAddress($newShop->getAddress());
        $shop->setZipCode($newShop->getZipCode());
        $shop->setCity($newShop->getCity());
        $shop->setImage($newShop->getImage());
        $shop->setOffer($newShop->getOffer());
        $shop->setIdShop($newShop->getIdShop());

        $this->getDoctrine()->getManager()->flush();

        return $shop;
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
