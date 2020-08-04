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
}
?>