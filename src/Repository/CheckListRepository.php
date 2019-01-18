<?php

namespace App\Repository;

use App\Entity\CheckList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CheckList|null find($id, $lockMode = null, $lockVersion = null)
 * @method CheckList|null findOneBy(array $criteria, array $orderBy = null)
 * @method CheckList[]    findAll()
 * @method CheckList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CheckListRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CheckList::class);
    }

    // /**
    //  * @return CheckList[] Returns an array of CheckList objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CheckList
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
