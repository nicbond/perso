<?php

namespace App\Service;

use GuzzleHttp\Client;
use JMS\Serializer\Serializer;

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

    public function __construct(Client $apiClient, Serializer $serializer)
    {
        $this->apiClient = $apiClient;
        $this->serializer = $serializer;
    }

    public function getConnexion()
    {
        $uri = '/testapi/shops';

        try {
            $response = $this->apiClient->get($uri);
        } catch (\Exception $e) {
            return ['error' => 'Les informations ne sont pas disponibles pour le moment.'];
        }
        $httpCode = $response->getStatusCode();
        var_dump($httpCode);die;

        $data = $this->serializer->deserialize($response->getBody()->getContents(), 'array', 'json');
        //var_dump($data);die;
    }
}
?>