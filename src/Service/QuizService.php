<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\Result;
use App\Entity\User;
use App\Repository\QuizRepository;
use App\Repository\ResultRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use function Doctrine\ORM\QueryBuilder;

class QuizService
{
    private const MAX_RESULT = 3;
    private QuizRepository $quizRepository;
    private PaginationInterface $pagination;
    /**
     * @var ResultRepository
     */
    private ResultRepository $resultRepository;

    /**
     * QuizService constructor.
     * @param QuizRepository $quizRepository
     * @param ResultRepository $resultRepository
     */
    public function __construct(QuizRepository $quizRepository, ResultRepository $resultRepository)
    {
        $this->quizRepository = $quizRepository;
        $this->resultRepository = $resultRepository;
    }

    public function getPagination(int $page)
    {
        $this->pagination = $this->quizRepository->findNext($page);
        return $this->pagination;
    }

    public function getLeadersForPage(int $page): array
    {
        $resultArray = [];
        if (!$this->pagination || $this->pagination->getCurrentPageNumber() !== $page) {
            $this->pagination = $this->quizRepository->findNext($page);
        }

        /** @var Quiz $quiz */
        foreach ($this->pagination->getItems() as $quiz) {
            $maxResult = array_reduce
            (
                $quiz->getResults()->toArray(),
                "self::maxResult",
                0
            );
            $resultsWithGivenResult = [];
            foreach ($quiz->getResults() as $result) {
                if ($result->getResult() === $maxResult) {
                    array_push($resultsWithGivenResult, $result);
                }
            }
            $minDuration = array_reduce($resultsWithGivenResult, "self::minDuration", -1);
            if ($minDuration === -1) {
                foreach ($quiz->getResults() as $result) {
                    if ($result->getResult() === $maxResult) {
                        $userName = $result->getUser()->getName();
                        break;
                    }
                }
            } else {
                /** @var Result $result */
                foreach ($resultsWithGivenResult as $result) {
                    if ($result->getEndDate()->getTimestamp() - $result->getStartDate()->getTimestamp() === $minDuration) {
                        $userName = $result->getUser()->getName();
                        break;
                    }
                }
            }
            $resultArray[$quiz->getId()] = $userName;
        }

        return $resultArray;
    }

    function maxResult(float $max, Result $r): float
    {
        return $max >= $r->getResult() ? $max : $r->getResult();
    }

    function minDuration(int $min, Result $r)
    {
        if (!$r->getEndDate()) {
            return $min;
        }
        $duration = $r->getEndDate()->getTimestamp() - $r->getStartDate()->getTimestamp();

        return $min <= $duration ? $min : $duration;
    }

    /**
     * @param User $user
     * @param Quiz $quiz
     * @return Result|null
     */
    public function getResult(User $user, Quiz $quiz): ?Result
    {
        return $this->resultRepository->findOneBy(['user' => $user, 'quiz' => $quiz]);
    }

    public function getTopLeaders(Quiz $quiz): array
    {
        $qb = $this->resultRepository->createQueryBuilder('r');
        return $qb
            ->where('r.quiz = :quiz')
            ->andWhere($qb->expr()->isNotNull('r.endDate'))
            ->addOrderBy('r.result', 'DESC')
            ->addOrderBy($qb->expr()->diff('r.endDate', 'r.startDate'))
            ->setMaxResults(self::MAX_RESULT)
            ->setParameter('quiz', $quiz)
            ->getQuery()
            ->getResult();
    }
}