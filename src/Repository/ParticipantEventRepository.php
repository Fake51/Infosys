<?php

namespace App\Repository;

use App\Entity\ParticipantEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ParticipantEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParticipantEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParticipantEvent[]    findAll()
 * @method ParticipantEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParticipantEvent::class);
    }

    // /**
    //  * @return ParticipantEvent[] Returns an array of ParticipantEvent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ParticipantEvent
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
