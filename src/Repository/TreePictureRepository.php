<?php

namespace App\Repository;

use App\Entity\TreePicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TreePicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method TreePicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method TreePicture[]    findAll()
 * @method TreePicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreePictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TreePicture::class);
    }

    // /**
    //  * @return TreePicture[] Returns an array of TreePicture objects
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
    public function findOneBySomeField($value): ?TreePicture
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
