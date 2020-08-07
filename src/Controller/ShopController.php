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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
     * @Rest\Post(
     *    path = "les-habitues/shops",
     *    name = "app_shop_create"
     * )
     * @Rest\View(StatusCode = 201)
     * @ParamConverter("shop", converter="fos_rest.request_body")
     */
    public function createAction(Shop $shop, ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }

            throw new ResourceValidationException($message);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($shop);
        $em->flush();

        return $this->view(
            $shop, 
            Response::HTTP_CREATED,
            ['Location' => $this->generateUrl('app_shop_show', ['id' => $shop->getId(), UrlGeneratorInterface::ABSOLUTE_URL])]);
    }

    /**
     * @Rest\Get(
     *     path = "les-habitues/shops/{id}",
     *     name = "app_shop_show",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     */
    public function show(Shop $shop)
    {
        return $shop;
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
