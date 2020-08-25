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
