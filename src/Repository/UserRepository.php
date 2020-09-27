<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param $id
     * @param $flag
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function changeUserIsActive($id, $flag): void
    {
        $user = $this->find($id)->setIsActive((bool)$flag);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateUserByAdmin(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param string|null $name
     * @param string|null $email
     * @return Query
     */
    public function search(?string $name, ?string $email): Query
    {
        return $this->createQueryBuilder('q')
            ->where('q.name like :name')
            ->andWhere('q.email like :email')
            ->setParameter('name', '%' . $name . '%')
            ->setParameter('email', '%' . $email . '%')
            ->getQuery();
    }
}