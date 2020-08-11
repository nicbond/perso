<?php

namespace App\Controller;

use App\Entity\Shop;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exception\ResourceValidationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Representation\Shops;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use App\Repository\ShopRepository;

use App\Service\Call;

class ShopController extends AbstractFOSRestController
{
    /**
     * @var PropertyRepository
     */
    private $repository;

    public function __construct(ShopRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Rest\Get("les-habitues/shops/list", name="app_shops_listing")
     * @Rest\QueryParam(
     *     name="keyword",
     *     requirements="[a-zA-Z0-9]",
     *     nullable=true,
     *     description="The keyword to search for."
     * )
     * @Rest\QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * @Rest\QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="25",
     *     description="Max number of shops per page."
     * )
     * @Rest\QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="0",
     *     description="The pagination offset"
     * )
     * @Rest\View()
     *
     */
    public function shopList($keyword, $order, $limit, $offset)
    {
        $pager = $this->repository->search($keyword, $order, $limit, $offset);
 
        return new Shops($pager);
    }

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
    public function create(Shop $shop, Call $call)
    {
        $controlData = $call->validatorData($shop);

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
    public function update(Shop $shop, Shop $newShop, Call $call)
    {
        $controlData = $call->validatorData($shop);

        $shop
            ->setNameShop($newShop->getNameShop())
            ->setAddress($newShop->getAddress())
            ->setZipCode($newShop->getZipCode())
            ->setCity($newShop->getCity())
            ->setImage($newShop->getImage())
            ->setOffer($newShop->getOffer())
            ->setIdShop($newShop->getIdShop());

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
