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
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

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
     * @SWG\Get(
     *     description="Get the list of all shops",
     *     tags={"Shops"},
     *     summary="Get the list of all shops"
     *),
     *     @SWG\Response(
     *         response=200,
     *         description="The request has succeeded"
     *     )
     * ),
     * @SWG\Tag(
     *   name="Shops"
     * )
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
     * @SWG\Get(
     *     description="Call api les-habitues and save or update the data if already exist",
     *     tags={"Technical test"},
     *     summary="Call api les-habitues and save or update the data if already exist"
     *),
     *     @SWG\Response(
     *         response=200,
     *         description="The request has succeeded"
     *     )
     * ),
     * @SWG\Tag(
     *   name="Technical test"
     * )
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
     * @SWG\Post(
     *     description="Create one shop",
     *     tags={"Shops"},
     *     summary="Create one shop",
     * @SWG\Parameter(
     *     name="name_shop",
     *     in="body",
     *     description="Name of the shop",
     *     type="string",
     * @SWG\Property(property="name_shop", type="string", example="les-habitues")
     * ),
     * @SWG\Parameter(
     *     name="address",
     *     in="body",
     *     description="Address of the shop",
     *     type="string",
     * @SWG\Property(property="address", type="string", example="48 Rue Sainte-Anne")
     * ),
     * @SWG\Parameter(
     *     name="zip_code",
     *     in="body",
     *     description="Zip code",
     *     type="string",
     * @SWG\Property(property="zip_code", type="string", example="75002")
     * ),
     * @SWG\Parameter(
     *     name="city",
     *     in="body",
     *     description="City Shop",
     *     type="string",
     * @SWG\Property(property="city", type="string", example="Paris")
     * ),
     * @SWG\Parameter(
     *     name="image",
     *     in="body",
     *     description="Image",
     *     type="string",
     * @SWG\Property(property="image", type="string", example="https://media.leshabitues.fr/shop/766/pic_35a79db69a94837a5228c983d066638e.jpg")
     * ),
     * @SWG\Parameter(
     *     name="offer",
     *     in="body",
     *     description="Offer",
     *     type="float",
     * @SWG\Property(property="offer", type="float", example="2.00")
     * ),
     * @SWG\Parameter(
     *     name="id_shop",
     *     in="body",
     *     description="ID Shop: by default the value will be 0",
     *     type="integer",
     * @SWG\Property(property="id_shop", type="integer", example="0")
     * ),
     *     @SWG\Response(
     *         response=201,
     *         description="Returned when created"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Returned when a violation is raised by validation"
     *     )
     * )
     * @SWG\Tag(
     *   name="Shops"
     * )
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
     * @SWG\Get(
     *     description="Get one shop",
     *     tags={"Shops"},
     *     summary="Get one shop",
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="ID de la ressource",
     *     type="integer",
     * @SWG\Schema(type="integer")
     * ),
     *     @SWG\Response(
     *         response=200,
     *         description="Get one shop"
     *     )
     * )
     * @SWG\Tag(
     *   name="Shops"
     * )
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
     * @SWG\Put(
     *     description="Update one shop",
     *     tags={"Shops"},
     *     summary="Update one shop"
     *),
     *     @SWG\Response(
     *         response=200,
     *         description="Get one shop"
     *     )
     * ),
     * @SWG\Tag(
     *   name="Shops"
     * )
     * @Rest\View(StatusCode = 200)
     * @Rest\Put(
     *     path = "les-habitues/shops/{id}",
     *     name = "app_shop_update",
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
     * @SWG\Delete(
     *     description="Delete one shop",
     *     tags={"Shops"},
     *     summary="Delete one shop"
     *),
     *     @SWG\Response(
     *         response=204,
     *         description="The server has successfully fulfilled the request and that there is no additional content to send in the response"
     *     )
     * ),
     * @SWG\Tag(
     *   name="Shops"
     * )
     * @Rest\View(StatusCode = 204)
     * @Rest\Delete(
     *     path = "les-habitues/shops/{id}",
     *     name = "app_shop_delete",
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
