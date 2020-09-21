<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Question;
use App\Entity\User;
use App\Repository\QuestionRepository;
use App\Repository\AnswerRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminService
{
    private const USERS_IN_PAGE = 5;
    private const QUESTIONS_IN_PAGE = 3;

    private QuestionRepository $questionRepository;
    private UserRepository $userRepository;
    private AnswerRepository $answerRepository;

    public function __construct(
        QuestionRepository $questionRepository,
        UserRepository $userRepository,
        AnswerRepository $answerRepository
    )
    {
        $this->questionRepository = $questionRepository;
        $this->userRepository = $userRepository;
        $this->answerRepository = $answerRepository;
    }

    public function getQuestions(): array
    {
        return $this->questionRepository->findAll();
    }

    public function saveNewQuiz(Request $request)
    {
        return $request->get('name');
    }

    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function blockUser($id, $flag): void
    {
        $this->userRepository->changeUserIsActive($id, $flag);
    }

    public function getUsersPage(PaginatorInterface $paginator, int $page)
    {

        return $paginator->paginate(
            $this->userRepository->getPaginatorQuery(),
            $page,
            self::USERS_IN_PAGE
        );
    }

    public function getUserById($id): User
    {

        return $this->userRepository->find($id);
    }

    public function updateUser($id, $name, $email, $verified): void
    {
        $user = $this->getUserById($id);
        $user->setName($name)->setEmail($email)->setIsVerified((bool)$verified);
        $this->userRepository->updateUserByAdmin($user);
    }

    public function saveNewQuestion(Question $question)
    {
        $this->questionRepository->saveQuestion($question);
    }

    public function getQuestionsPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->questionRepository->getPaginatorQuery(),
            $page,
            self::QUESTIONS_IN_PAGE
        );
    }
}