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

    /*method have its own validation for unique questions*/
    public function saveNewQuiz(Quiz $quiz): bool
    {
        $questionsNames = [];
        foreach ($quiz->getQuestions() as $question) {
            $questionsNames[] = $question->getName();
        }
        if (count(array_unique($questionsNames)) < count($questionsNames)) {
            return false;
        }
        if (count($questionsNames) < 1) return false;

        $quiz->setCreateDate(new DateTime());
        $this->quizRepository->saveQuiz($quiz);

        return true;
    }

    /*method ask repository for query in db, to get part of entities*/
    public function getUsersPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->userRepository->getPaginatorQuery(),
            $page,
            self::USERS_IN_PAGE
        );
    }

    /*method validates answers count and save question*/
    public function saveQuestion(Question $question): bool
    {
        if (!$question->getAnswers()->isEmpty()) {
            $this->questionRepository->saveQuestion($question);

            return true;
        }
        return false;
    }

    /*method ask question repository for query in db for part of entities*/
    public function getQuestionsPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->questionRepository->getPaginatorQuery(),
            $page,
            self::QUESTIONS_IN_PAGE
        );
    }

    /*method removes and deletes all answer from a question, and then try to delete question*/
    /*if it falls, method restore answers in question*/
    public function deleteQuestionById($id): bool
    {
        $question = $this->questionRepository->find($id);
        $answers = $question->getAnswers();
        foreach ($answers as $answer) {
            $question->removeAnswer($answer);
        }
        if($this->questionRepository->deleteQuestion($question))
        {
            foreach ($answers as $answer) {
                $this->answerRepository->deleteAnswer($answer);
            }

            return true;
        }else{
            foreach ($answers as $answer) {
                $question->addAnswer($answer);
            }

            return false;
        }
    }
    /*return part of quiz entities for pagination*/
    public function getQuizesPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->quizRepository->getPaginatorQuery(),
            $page,
            self::QUIZES_IN_PAGE
        );
    }
    /*method removes questions from an answer, and then try to delete it*/
    /*if falls, restores all questions*/
    public function deleteQuizById($id): bool
    {
        $quiz = $this->quizRepository->find($id);
        $questions = $quiz->getQuestions();
        foreach ($questions as $question) {
            $quiz->removeQuestion($question);
        }
        if(!$this->quizRepository->deleteQuiz($quiz))
        {
            foreach ($questions as $question) {
                $quiz->addQuestion($question);
            }
            return false;
        }
        return true;
    }
}