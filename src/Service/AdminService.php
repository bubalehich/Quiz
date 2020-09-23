<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\User;
use App\Repository\QuestionRepository;
use App\Repository\AnswerRepository;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;

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

    public function saveNewQuiz(Quiz $quiz) :bool
    {
        $questionsNames = [];
        foreach($quiz->getQuestions() as $question){
            $questionsNames[] = $question->getName();
        }
       if(count(array_unique($questionsNames))<count($questionsNames)){
           return false;
       }
       if(count($questionsNames)<1)return false;

       $quiz->setCreateDate(new DateTime());
       $this->quizRepository->saveQuiz($quiz);

       return true;
    }

    public function getUsersPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->userRepository->getPaginatorQuery(),
            $page,
            self::USERS_IN_PAGE
        );
    }

    public function updateUser($id, $name, $email, $verified): void
    {
        $user = $this->getUserById($id);
        $user->setName($name)->setEmail($email)->setIsVerified((bool)$verified);
        $this->userRepository->updateUserByAdmin($user);
    }

    public function saveQuestion(Question $question): bool
    {
        if (!$question->getAnswers()->isEmpty())
        {
            $this->questionRepository->saveQuestion($question);

            return true;
        }
            return false;
    }

    public function getQuestionsPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->questionRepository->getPaginatorQuery(),
            $page,
            self::QUESTIONS_IN_PAGE
        );
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

    public function deleteQuizById($id): void
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