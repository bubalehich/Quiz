<?php
declare(strict_types=1);

namespace App\Service\AdminServices;


use App\Repository\QuizRepository;
use App\Repository\QuizUserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;
use App\Entity\Quiz;

class AdminService
{
    private $quizUserRepository;
    private $quizRepository;

    public function __construct(QuizUserRepository $quizUserRepository, QuizRepository $quizRepository)
    {
        $this->quizUserRepository = $quizUserRepository;
        $this->quizRepository = $quizRepository;
    }

    public function getUsersPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->quizUserRepository->getPaginatorQuery(),
            $page,
            5
        );
    }
    public function getQuizesPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->quizRepository->getPaginatorQuery(),
            $page,
            5
        );
    }

    public function addQuizFromForm(FormInterface $quizForm)
    {
        $quiz = $quizForm->getData();
        $quiz->setIsActive(1)
            ->setCreateDate(new \DateTime("now"))
            ->setUsersCount(0);
        $this->addQuiz($quiz);
    }
    public function addQuiz(Quiz $quiz){
        $this->quizRepository->addNewQuiz($quiz);
    }
}