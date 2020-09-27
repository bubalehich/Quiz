<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Repository\QuestionRepository;
use App\Repository\AnswerRepository;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class AdminService
{
    private const USERS_PER_PAGE = 5;
    private const QUESTIONS_PER_PAGE = 3;
    private const QUIZZES_PER_PAGE = 5;

    private QuestionRepository $questionRepository;
    private UserRepository $userRepository;
    private AnswerRepository $answerRepository;
    private QuizRepository $quizRepository;

    /**
     * AdminService constructor.
     * @param QuestionRepository $questionRepository
     * @param UserRepository $userRepository
     * @param AnswerRepository $answerRepository
     * @param QuizRepository $quizRepository
     */
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

    /**
     * @param Quiz $quiz
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveNewQuiz(Quiz $quiz): bool
    {
        $questionsNames = [];
        foreach ($quiz->getQuestions() as $question) {
            $questionsNames[] = $question->getName();
        }
        if (count(array_unique($questionsNames)) < count($questionsNames) || (count($questionsNames) < 1)) {
            return false;
        }

        $quiz->setCreateDate(new DateTime());
        $this->quizRepository->saveQuiz($quiz);

        return true;
    }

    /**
     * @param Question $question
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveQuestion(Question $question): bool
    {
        if (!$question->getAnswers()->isEmpty()) {
            $this->questionRepository->saveQuestion($question);

            return true;
        }
        return false;
    }

    /**
     * @param PaginatorInterface $paginator
     * @param int $page
     * @param string|null $name
     * @return PaginationInterface
     */
    public function getQuestionsPage(PaginatorInterface $paginator, int $page, ?string $name): PaginationInterface
    {
        return $paginator->paginate(
            $this->questionRepository->getPaginationQuery($name),
            $page,
            self::QUESTIONS_PER_PAGE
        );
    }

    /**
     * @param $id
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteQuestionById($id): void
    {
        $question = $this->questionRepository->find($id);
        foreach ($question->getAnswers() as $answer) {
            $question->removeAnswer($answer);
            $this->answerRepository->deleteAnswer($answer);
        }
        $this->questionRepository->deleteQuestion($question);
    }

    /**
     * @param PaginatorInterface $paginator
     * @param int $page
     * @param string|null $name
     * @return PaginationInterface
     */
    public function getQuizzesPage(PaginatorInterface $paginator, int $page, ?string $name): PaginationInterface
    {
        return $paginator->paginate(
            $this->quizRepository->getPaginationQuery($name),
            $page,
            self::QUIZZES_PER_PAGE
        );
    }

    /**
     * @param $id
     */
    public function deleteQuizById($id): void
    {
        $quiz = $this->quizRepository->find($id);
        foreach ($quiz->getQuestions() as $question) {
            $quiz->removeQuestion($question);
        }
        $this->quizRepository->deleteQuiz($quiz);
    }

    /**
     * @param PaginatorInterface $paginator
     * @param int $page
     * @param string|null $name
     * @param string|null $email
     * @return PaginationInterface
     */
    public function getUsersPage(PaginatorInterface $paginator, int $page, ?string $name, ?string $email): PaginationInterface
    {
        return $paginator
            ->paginate($this->userRepository->search($name, $email), $page, self::USERS_PER_PAGE);
    }

    /**
     * @param $id
     * @return Quiz
     */
    public function getQuizById($id): Quiz
    {
        return $this->quizRepository->find($id);
    }
}