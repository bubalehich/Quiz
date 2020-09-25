<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ConnectionException;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function saveQuestion(Question $question) :void
    {
        $this->_em->persist($question);
        $this->_em->flush();
    }

    public function getPaginatorQuery(?string $name)
    {
        return $this->createQueryBuilder('q')
            ->where('q.name like :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery();
    }

    public function deleteQuestion(Question $question): bool
    {
        try {
            $this->_em->remove($question);
            $this->_em->flush();
        }catch(Exception $exception){
            return false;
        }
        return true;
    }
}