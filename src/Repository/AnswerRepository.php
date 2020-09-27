<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    /**
     * AnswerRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    /**
     * @param Answer $answer
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveNewAnswer(Answer $answer):void
    {
        $this->_em->persist($answer);
        $this->_em->flush();
    }

    /**
     * @param Answer $answer
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAnswer(Answer $answer): void
    {
        $this->_em->remove($answer);
        $this->_em->flush();
    }
}