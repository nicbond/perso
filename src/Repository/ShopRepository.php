<?php

namespace App\Repository;

use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[]    findAll()
 * @method Shop[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shop::class);
    }
    
    public function search($term, $order = 'asc', $limit = 25, $offset = 0)
    {
        $qb = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->orderBy('s.nameShop', $order)
        ;

        if ($term) {
            $qb
                ->where('s.nameShop LIKE ?1')
                ->setParameter(1, '%'.$term.'%')
            ;
        }

        return $this->paginate($qb, $limit, $offset);
    }

    public function paginate(QueryBuilder $qb, $limit = 25, $offset = 0)
    {
        $limit = (int) $limit;

        if (0 === $limit) {
            throw new \LogicException('$limit must be greater than 0.');
        }
 
        $pager = new Pagerfanta(new QueryAdapter($qb));
        $pager->setCurrentPage(ceil(($offset + 1) / $limit));
        $pager->setMaxPerPage((int) $limit);
 
        return $pager;
    }
}
