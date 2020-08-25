<?php

namespace App\Service;

use App\Entity\Shop;
use GuzzleHttp\Client;
use PSR\Log\LoggerInterface;
use JMS\Serializer\Serializer;
use App\Repository\ShopRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    )
    {
        $this->apiClient = $apiClient;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function methodHttp($method, $url, $data, $options = [])
    {
        $options['body'] = $this->serializer->serialize($data, 'json');
        $options['headers'] = ['Content-Type' => 'application/json'];

        try {
            switch($method) {
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
