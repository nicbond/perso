<?php

namespace App\Service;

use App\Entity\Shop;
use GuzzleHttp\Client;
use PSR\Log\LoggerInterface;
use JMS\Serializer\Serializer;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\HttpFoundation\Response;

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
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        Client $apiClient,
        Serializer $serializer,
        LoggerInterface $logger,
        ShopRepository $repository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    )
    {
        $this->apiClient = $apiClient;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function getConnexion(): Response
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
        
        if ($this->getData($data) == true) {
            $response = new Response('SHOP CREATED OR UPDATED', Response::HTTP_CREATED);
        }

        return $response;
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
                    $shop
                            ->setNameShop($data['data'][$i]['chain'])
                            ->setAddress($data['data'][$i]['localisations'][0]['address'])
                            ->setZipCode($data['data'][$i]['localisations'][0]['zipcode'])
                            ->setCity($data['data'][$i]['localisations'][0]['city'])
                            ->setImage($data['data'][$i]['picture_url'])
                            ->setOffer($data['data'][$i]['offers'][0]['reduction'])
                            ->setIdShop($data['data'][$i]['objectID']);
                        
                    $this->validatorData($shop); //Data control

                    $this->entityManager->persist($shop);
                } else {
                    $shopAlreadyExist = $this->repository->find($shopSearch->getId());
                    $shopAlreadyExist
                            ->setNameShop($data['data'][$i]['chain'])
                            ->setAddress($data['data'][$i]['localisations'][0]['address'])
                            ->setZipCode($data['data'][$i]['localisations'][0]['zipcode'])
                            ->setCity($data['data'][$i]['localisations'][0]['city'])
                            ->setImage($data['data'][$i]['picture_url'])
                            ->setOffer($data['data'][$i]['offers'][0]['reduction'])
                            ->setIdShop($data['data'][$i]['objectID']);

                    $this->validatorData($shopAlreadyExist); //Data control

                    $this->entityManager->persist($shopAlreadyExist);
                }
                $this->entityManager->flush();
            } catch (\Doctrine\ORM\ORMException $e) {
                $errorMsg = 'Error Doctrine for the id_shop '.$data['data'][$i]['objectID'].'<br/>'.$e->getMessage();
            }
            $i++;
        } while ($i <= $size);
        return true;
    }

    public function validatorData(Shop $shop)
    {
        $violations = $this->validator->validate($shop);

        if (count($violations)) {
            $message = 'Invalid data. Here are the errors you need to correct: ' .'</br>';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
                $message .= '</br>';
            }
            throw new \Exception($message);
        }
    }
}
