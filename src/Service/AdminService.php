<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\QuestionRepository;
use App\Repository\AnswerRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminService
{
    private const USERS_IN_PAGE = 5;

    private QuestionRepository $questionRepository;
    private UserRepository $userRepository;

    public function __construct(
        QuestionRepository $questionRepository,
        UserRepository $userRepository
    ) {
        $this->questionRepository = $questionRepository;
        $this->userRepository = $userRepository;
    }

    public function getQuestions()
    {
        return $this->questionRepository->findAll();
    }

    public function saveNewQuiz(Request $request)
    {
        return $request->get('name');
    }

    public function getUsers()
    {
        return $this->userRepository->findAll();
    }

    public function blockUser($id,$flag):void
    {
        $this->userRepository->changeUserIsActive($id,$flag);
    }

    public function getUsersPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->userRepository->getPaginatorQuery(),
            $page,
            self::USERS_IN_PAGE
        );
    }
}