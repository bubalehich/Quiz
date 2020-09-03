<?php

namespace App\Service;

use App\Entity\Quiz;
use App\Entity\Result;
use App\Repository\QuizRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;

class QuizService
{
    private QuizRepository $quizRepository;
    private PaginationInterface $pagination;

    /**
     * QuizService constructor.
     * @param QuizRepository $quizRepositor
     */
    public function __construct(QuizRepository $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    public function getPagination(int $page)
    {
        $this->pagination = $this->quizRepository->findNext($page);
        return $this->pagination;
    }

    public function getLeadersForPage(int $page): array
    {
        $resultArray = [];
        if(!$this->pagination||$this->pagination->getCurrentPageNumber()!==$page){
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
            $resultArray[$quiz->getId()]= $userName;
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
}