<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{
    /**
     * QuizRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    /**
     * @param string|null $name
     * @return Query
     */
    public function getPaginationQuery(?string $name): Query
    {
        return $this->createQueryBuilder('q')
            ->where('q.name like :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();
    }

    /**
     * @param Quiz $quiz
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveQuiz(Quiz $quiz): void
    {
        $this->_em->persist($quiz);
        $this->_em->flush();
    }

    /**
     * @param Quiz $quiz
     * @return bool
     */
    public function deleteQuiz(Quiz $quiz): bool
    {
        try {
            $this->_em->remove($quiz);
            $this->_em->flush();
        } catch (Exception $exception) {
            return false;
        }
        return true;
    }
}