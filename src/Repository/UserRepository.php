<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function changeUserIsActive($id, $flag): void
    {
        $user = $this->find($id)->setIsActive((bool)$flag);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function updateUserByAdmin(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush();
    }

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