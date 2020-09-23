<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\QuestionRepository;
use App\Repository\AnswerRepository;
use App\Repository\QuizRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminService
{
    private const USERS_IN_PAGE = 5;
    private const QUESTIONS_IN_PAGE = 3;
    private const QUIZES_IN_PAGE = 5;

    private QuestionRepository $questionRepository;
    private UserRepository $userRepository;
    private AnswerRepository $answerRepository;
    private QuizRepository $quizRepository;

    public function __construct(
        QuestionRepository $questionRepository,
        UserRepository $userRepository,
        AnswerRepository $answerRepository,
        QuizRepository $quizRepository
    )
    {
        $this->questionRepository = $questionRepository;
        $this->userRepository = $userRepository;
        $this->answerRepository = $answerRepository;
        $this->quizRepository = $quizRepository;
    }

    public function getQuestions(): array
    {
        return $this->questionRepository->findAll();
    }

    public function saveNewQuiz(Quiz $quiz)
    {
        $questionsNames = [];
        foreach($quiz->getQuestions() as $question){
            $questionsNames[] = $question->getName();
        }
       if(count(array_unique($questionsNames))<count($questionsNames)){
           return false;
       }
       if(count($questionsNames)<1)return false;

       $quiz->setCreateDate(new \DateTime());
       $this->quizRepository->saveQuiz($quiz);
       return true;
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

    public function saveQuestion(Question $question)
    {
        if (count($question->getAnswers()) > 0)
        {
            $this->questionRepository->saveQuestion($question);
            return true;
        } else {
            return false;
        }
    }

    public function getQuestionsPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->questionRepository->getPaginatorQuery(),
            $page,
            self::QUESTIONS_IN_PAGE
        );
    }

    public function getQuestionById($id): ?Question
    {
        return $this->questionRepository->find($id);
    }

    public function deleteQuestionById($id): void
    {
      $question = $this->questionRepository->find($id);
        foreach($question->getAnswers() as $answer){
            $question->removeAnswer($answer);
            $this->answerRepository->deleteAnswer($answer);
        }

        $this->questionRepository->deleteQuestion($question);
    }

    public function getQuizesPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->quizRepository->getPaginatorQuery(),
            $page,
            self::QUIZES_IN_PAGE
        );
    }

    public function deleteQuizById($id)
    {
        $quiz = $this->quizRepository->find($id);
        foreach($quiz->getQuestions() as $question){
            $quiz->removeQuestion($question);
        }
        $this->quizRepository->deleteQuiz($quiz);
    }

    public function getQuizById($id): Quiz
    {
        return $this->quizRepository->find($id);
    }

}