<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Quiz;
use App\Repository\UserRepository;
use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;


class AdminService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsersPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->userRepository->getPaginatorQuery(),
            $page,
            5
        );
    }
    public function getQuizesPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->userRepository->getPaginatorQuery(),
            $page,
            5
        );
    }

    public function addQuizFromForm(FormInterface $quizForm)
    {

    }
    public function addQuiz(Quiz $quiz){

    }
}