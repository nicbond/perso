<?php

namespace App\Service;

use App\Entity\Shop;
use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use PSR\Log\LoggerInterface;
use App\Repository\ShopRepository;

class Call
{
    /**
     * @var Client
     */
    private $apiClient;
    /**
     * @var Serializer
     */
    private $serializer;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ShopRepository
     */
    private $repository;

    public function __construct(Client $apiClient, Serializer $serializer, LoggerInterface $logger, ShopRepository $repository)
    {
        $this->apiClient = $apiClient;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->repository = $repository;
    }

    public function getConnexion()
    {
        $uri = '/testapi/shops';

        try {
            $response = $this->apiClient->get($uri);
        } catch (\Exception $e) {
            $this->logger->error('Les informations ne sont pas disponibles pour le moment.');
            return ['error' => 'Les informations ne sont pas disponibles pour le moment.'];
        }
        $httpCode = $response->getStatusCode();

        if ($httpCode == 200) {
            $data = $this->serializer->deserialize($response->getBody()->getContents(), 'array', 'json');
            $this->getData($data);
        }
    }
    
    public function getData(array $data)
    {
        $size = count($data['data']); //20 résultats
        $size = $size-1; // -1 à cause de l'indice 0
        $i = 0;

        do {
            $id_shop = $data['data'][$i]['objectID'];
            $shopSearch = $this->repository->findOneBy(array('id_shop' => $id_shop));

                try {
                    if (is_null($shopSearch)) {
                        $shop = new Shop();
                        $shop->setNameShop($data['data'][$i]['chain']);
                        $shop->setAddress($data['data'][$i]['localisations'][0]['address']);
                        $shop->setZipCode($data['data'][$i]['localisations'][0]['zipcode']);
                        $shop->setCity($data['data'][$i]['localisations'][0]['city']);
                        $shop->setImage($data['data'][$i]['picture_url']);
                        $shop->setOffer($data['data'][$i]['offers'][0]['reduction']);
                        $shop->setIdShop($data['data'][$i]['objectID']);
                    } else {
                        $shopAlreadyExist = $this->repository->find($shopSearch->getId());
                        $shopAlreadyExist->setNameShop($data['data'][$i]['chain']);
                        $shopAlreadyExist->setAddress($data['data'][$i]['localisations'][0]['address']);
                        $shopAlreadyExist->setZipCode($data['data'][$i]['localisations'][0]['zipcode']);
                        $shopAlreadyExist->setCity($data['data'][$i]['localisations'][0]['city']);
                        $shopAlreadyExist->setImage($data['data'][$i]['picture_url']);
                        $shopAlreadyExist->setOffer($data['data'][$i]['offers'][0]['reduction']);
                        $shopAlreadyExist->setIdShop($data['data'][$i]['objectID']);
                    }
                } catch (\Doctrine\ORM\ORMException $e) {
                    $errorMsg = 'Error Doctrine for the id_shop '.$data['data'][$i]['objectID'].'<br/>'.$e->getMessage();
                }
            $i++;
        } while ($i <= $size);
    }
}
?>