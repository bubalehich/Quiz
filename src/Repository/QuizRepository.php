<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    public function getPaginatorQuery(?string $name): Query
    {
        return $this->createQueryBuilder('q')
            ->where('q.name like :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();
    }

    public function saveQuiz(Quiz $quiz): void
    {
        $this->_em->persist($quiz);
        $this->_em->flush();
    }

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