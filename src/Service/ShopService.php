<?php

namespace App\Service;

use App\Entity\Shop;
use App\Repository\ShopRepository;

final class ShopService
{
    /**
     * @var ShopRepository
     */
    private $repository;

    public function __construct(ShopRepository $repository)
    {
        $this->repository = $repository;
    }
}
