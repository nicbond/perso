<?php

namespace App\Service;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use PSR\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ShopService
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
    ) {
        $this->apiClient = $apiClient;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function methodHttp($method, $url, Shop $data, $options = []): Response
    {
        $id = $data->getId(); // Je récupére mon id pour la suppression de l'objet dans ma base
        $options['body'] = $this->serializer->serialize($data, 'json');
        $options['headers'] = ['Content-Type' => 'application/json'];

        try {
            switch ($method) {
                case 'post':
                    $response = $this->apiClient->post($url, $options);
                    break;
                case 'put':
                    $response = $this->apiClient->put($url, $options);
                    break;
                case 'delete':
                    $response = $this->apiClient->delete($url);
                    break;
                case 'get':
                    $response = $this->apiClient->get($url);
                    break;
                default:
                    throw new \Exception('Missing request Method');
            }
        } catch (\Exception $e) {
            $this->logger->error('Les informations ne sont pas disponibles pour le moment.');

            return ['error' => 'Les informations ne sont pas disponibles pour le moment.'];
        }

        $httpCode = $response->getStatusCode();
        $data = $this->serializer->deserialize($response->getBody()->getContents(), 'array', 'json');
        $response = $this->getHttpCode($httpCode, $id, $data);

        return $response;
    }

    public function getHttpCode($httpCode, $id, array $data): Response
    {
        //code 200 : method GET / UPDATE succeed
        //code 201 : method POST succeed
        //code 204 : method DELETE succeed

        switch ($httpCode) {
            case 200:
                $this->getData($data);
                $response = new Response('SHOP UPDATED', Response::HTTP_OK);
                break;
            case 201:
                $this->getData($data);
                $response = new Response('SHOP CREATED', Response::HTTP_CREATED);
                break;
            case 204:
                $shop = $this->repository->find($id);
                $this->entityManager->remove($shop);
                $this->entityManager->flush();
                $response = new Response('SHOP DELETED', Response::HTTP_OK);
                break;
            case 400:
                $response = new Response('INVALID DATA', Response::HTTP_BAD_REQUEST);
                break;
            case 404:
                $response = new Response('API NOT FOUND', Response::HTTP_NOT_FOUND);
                break;
            case 500:
                $response = new Response('INTERNAL SERVER ERROR', Response::HTTP_INTERNAL_SERVER_ERROR);
                break;
            default:
                $response = new Response('UNDOCUMENTED ERROR', Response::HTTP_INTERNAL_SERVER_ERROR);
                break;
        }

        return $response;
    }

    public function getData(array $data)
    {
        $size = count($data['data']);
        $size = $size - 1;
        $i = 0;

        do {
            $id_shop = $data['data'][$i]['objectID'];
            $shopSearch = $this->repository->findOneBy(['id_shop' => $id_shop]);

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
            ++$i;
        } while ($i <= $size);
    }

    public function validatorData(Shop $shop)
    {
        $violations = $this->validator->validate($shop);

        if (count($violations)) {
            $message = 'Invalid data. Here are the errors you need to correct: '.'</br>';
            foreach ($violations as $violation) {
                $message .= sprintf('Field %s: %s ', $violation->getPropertyPath(), $violation->getMessage());
                $message .= '</br>';
            }
            throw new \Exception($message);
        }
    }
}
