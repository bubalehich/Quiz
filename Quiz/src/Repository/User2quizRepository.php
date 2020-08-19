<?php

namespace App\Repository;

use App\Entity\User2quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User2quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method User2quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method User2quiz[]    findAll()
 * @method User2quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class User2quizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User2quiz::class);
    }

    // /**
    //  * @return User2quiz[] Returns an array of User2quiz objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User2quiz
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
