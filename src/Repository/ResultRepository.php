<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Quiz;
use App\Entity\Result;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Result|null find($id, $lockMode = null, $lockVersion = null)
 * @method Result|null findOneBy(array $criteria, array $orderBy = null)
 * @method Result[]    findAll()
 * @method Result[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultRepository extends ServiceEntityRepository
{
    /**
     * ResultRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Result::class);
    }

    /**
     * @param Quiz $quiz
     * @return QueryBuilder
     */
    public function getLeaders(Quiz $quiz): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r');

        return $qb
            ->where('r.quiz = :quiz')
            ->andWhere($qb->expr()->isNotNull('r.endDate'))
            ->addOrderBy('r.result', 'DESC')
            ->addOrderBy($qb->expr()->diff('r.endDate', 'r.startDate'))
            ->setParameter('quiz', $quiz);
    }
}