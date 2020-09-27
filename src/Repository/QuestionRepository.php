<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    /**
     * QuestionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @param Question $question
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveQuestion(Question $question): void
    {
        $this->_em->persist($question);
        $this->_em->flush();
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
     * @param Question $question
     * @return bool
     */
    public function deleteQuestion(Question $question): bool
    {
        try {
            $this->_em->remove($question);
            $this->_em->flush();
        } catch (Exception $exception) {
            return false;
        }
        return true;
    }
}