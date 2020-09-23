<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Quiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quiz[]    findAll()
 * @method Quiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function findNext(int $page): Query
    {
        return $this
            ->createQueryBuilder('q')
            ->select()
            ->getQuery();
    }

    public function getPaginatorQuery()
    {
        $dql = "SELECT i FROM App\Entity\Quiz i";
        return $this->_em->createQuery($dql);
    }

    public function saveQuiz(Quiz $quiz): void
    {
        $this->_em->persist($quiz);
        $this->_em->flush();
    }

    public function deleteQuiz(Quiz $quiz): void
    {
        $this->_em->remove($quiz);
        $this->_em->flush();
    }

}