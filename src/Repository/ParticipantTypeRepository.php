<?php

namespace App\Repository;

use App\Entity\ParticipantType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ParticipantType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParticipantType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParticipantType[]    findAll()
 * @method ParticipantType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParticipantType::class);
    }

    // /**
    //  * @return ParticipantType[] Returns an array of ParticipantType objects
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
    public function findOneBySomeField($value): ?ParticipantType
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
