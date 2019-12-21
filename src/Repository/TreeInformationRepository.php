<?php

namespace App\Repository;

use App\Entity\TreeInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TreeInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TreeInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TreeInformation[]    findAll()
 * @method TreeInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreeInformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TreeInformation::class);
    }

    // /**
    //  * @return TreeInformation[] Returns an array of TreeInformation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TreeInformation
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
