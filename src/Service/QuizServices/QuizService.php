<?php
declare(strict_types=1);

namespace App\Service\QuizServices;


use App\Repository\QuizRepository;

class QuizService
{
    private $quizRepository;
    public function __construct(QuizRepository $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }
    public function getQuizes(){
        return $this->quizRepository->findAll();
    }
}