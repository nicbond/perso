<?php

namespace App\Service;

use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use PSR\Log\LoggerInterface;

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

    public function __construct(Client $apiClient, Serializer $serializer, LoggerInterface $logger)
    {
        $this->apiClient = $apiClient;
        $this->serializer = $serializer;
        $this->logger = $logger;
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
        var_dump($httpCode);die;

        $data = $this->serializer->deserialize($response->getBody()->getContents(), 'array', 'json');
        //var_dump($data);die;
    }
}
?>