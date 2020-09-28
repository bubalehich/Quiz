<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Quiz;
use App\Entity\User;
use App\Form\QuizProcessFormType;
use App\Service\QuizService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuizController
 * @IsGranted("ROLE_USER")
 */
class QuizController extends AbstractController
{
    private QuizService $service;
    private EntityManagerInterface $em;

    /**
     * QuizController constructor.
     * @param QuizService $service
     * @param EntityManagerInterface $em
     */
    public function __construct(QuizService $service, EntityManagerInterface $em)
    {
        $this->service = $service;
        $this->em = $em;
    }

    /**
     * @Route("/quizes", name="app_quizes")
     * @param Request $request
     * @return Response
     * @throws NonUniqueResultException
     */
    public function onQuizzesPage(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $searchCriteria = $request->query->get('search');

        $pagination = $this->service->getPaginationQuizzes($page, $searchCriteria);
        $leaders = $this->service->getLeadersForPage($pagination);

        return $this->render
        (
            'quiz/all_quizes.html.twig',
            ['pagination' => $pagination, 'leaders' => $leaders]
        );
    }

    /**
     * @Route ("/quiz_info/{id}", name="app_quiz_info")
     * @param Quiz $quiz
     * @return Response
     */
    public function quizInfo(Quiz $quiz): Response
    {
        /**@var User $user */
        $user = $this->getUser();
        $result = $this->service->getResult($user, $quiz);
        $topResults = $this->service->findTopLeaders($quiz);
        $rate = $result && $result->getEndDate() ? $this->service->findUserPlace($user, $quiz) : null;

        return $this->render('quiz/quiz_info.html.twig', [
            'quiz' => $quiz,
            'topResults' => $topResults,
            'result' => $result,
            'rate' => $rate,
        ]);
    }

    /**
     * @Route ("/quiz_info/{id}/leaders", name="app_quiz_leaders")
     * @param Quiz $quiz
     * @param Request $request
     * @return Response
     */
    public function leaderBoard(Quiz $quiz, Request $request)
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->service->getPaginateLeaders($quiz, $page);

        return $this->render('quiz/leaderboard.html.twig', [
            'quiz' => $quiz,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route ("/quiz/{id}", name="app_quiz")
     * @param Quiz $quiz
     * @param Request $request
     * @return Response
     */
    public function proceed(Quiz $quiz, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $result = $this->service->startParticipate($quiz, $user);

        if (!($quiz->getIsActive()) || $result->getEndDate()) {
            return $this->redirectToRoute('app_quiz_info', ['id' => $quiz->getId()]);
        }

        foreach ($quiz->getQuestions() as $question) {
            if (!$this->service->isProceed($question, $result)) {
                $form = $this->createForm(QuizProcessFormType::class, null, ['question' => $question]);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    /** @var Answer $answer */
                    $answer = ($form->getData())['answer'];
                    $this->service->createNewProgress($result, $answer);

                    $request->getSession()->set('question', $question->getName());
                    $request->getSession()->set('answer', $answer->getName());
                    $request->getSession()->set('isRight', $answer->getIsRight());
                    $request->getSession()->set('progress', $result->getProgress()->count());

                    return $this->redirectToRoute('app_show_answer', [
                        'id' => $quiz->getId(),
                    ]);
                }

                return $this->render('quiz/proceed.html.twig', [
                    'form' => $form->createView(),
                    'quiz' => $quiz,
                    'result' => $result,
                    'question' => $question]);
            }
        }
        $result->setEndDate(new DateTime());
        $this->em->persist($result);
        $this->em->flush();

        return $this->redirectToRoute('app_quiz_info', ['id' => $quiz->getId()]);
    }

    /**
     * @Route ("/quiz/{id}/answer", name = "app_show_answer")
     * @param Quiz $quiz
     * @param Request $request
     * @return Response
     */
    public function showAnswer(Quiz $quiz, Request $request): Response
    {
        $session = $request->getSession();
        $question = $session->remove('question');
        $answer = $session->remove('answer');
        $progress = $session->remove('progress');
        $isRight = $session->remove('isRight');
        if (!$question || !$answer || $isRight === null) {
            return $this->redirectToRoute('app_quiz', ['id' => $quiz->getId()]);
        }

        return $this->render('quiz/proceed_answer.html.twig', [
            'question' => $question,
            'quiz' => $quiz,
            'answer' => $answer,
            'isRight' => $isRight,
            'progress' => $progress
        ]);
    }
}