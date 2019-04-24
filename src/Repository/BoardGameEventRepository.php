<?php

namespace App\Repository;

use App\Entity\BoardGameEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method BoardGameEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoardGameEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoardGameEvent[]    findAll()
 * @method BoardGameEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardGameEventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, BoardGameEvent::class);
    }

    // /**
    //  * @return BoardGameEvent[] Returns an array of BoardGameEvent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BoardGameEvent
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
