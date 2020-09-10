<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\Result;
use App\Entity\User;
use App\Repository\QuizRepository;
use App\Repository\ResultRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class QuizService
{
    private const MAX_RESULT = 3;
    private const PAGINATION_QUIZES_LIMIT = 7;
    private const PAGINATION_LEADERS_LIMIT = 7;
    private QuizRepository $quizRepository;
    private ResultRepository $resultRepository;
    private PaginatorInterface $paginator;

    /**
     * QuizService constructor.
     * @param QuizRepository $quizRepository
     * @param ResultRepository $resultRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(QuizRepository $quizRepository, ResultRepository $resultRepository, PaginatorInterface $paginator)
    {
        $this->quizRepository = $quizRepository;
        $this->resultRepository = $resultRepository;
        $this->paginator = $paginator;
    }

    public function getPaginateQuizes(int $page): PaginationInterface
    {
        return $this
            ->paginator
            ->paginate($this->quizRepository->findNext($page), $page, self::PAGINATION_QUIZES_LIMIT);
    }

    public function getLeadersForPage(int $page): array
    {
        $resultArray = [];
        /** @var Quiz $quiz */
        foreach ($this->getPaginateQuizes($page)->getItems() as $quiz) {
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
        $qb = $this->resultRepository->getLeaders($quiz);
        return $qb
            ->setMaxResults(self::MAX_RESULT)
            ->getQuery()
            ->getResult();
    }

    public function getPaginateLeaders(Quiz $quiz, int $page): PaginationInterface
    {
        $query = $this->resultRepository->getLeaders($quiz)->getQuery();
        return $this->paginator->paginate($query, $page, self::PAGINATION_LEADERS_LIMIT);
    }
}