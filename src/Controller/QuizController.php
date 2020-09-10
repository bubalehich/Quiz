<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Quiz;
use App\Service\QuizService;
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

    /**
     * QuizController constructor.
     * @param QuizService $service
     */
    public function __construct(QuizService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/quizes", name="app_quizes")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->service->getPaginateQuizes($page);
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
        $result = $this->service->getResult($this->getUser(), $quiz);
        $topResults = $this->service->getTopLeaders($quiz);
        return $this->render('quiz/quiz_info.html.twig', [
            'quiz' => $quiz,
            'topResults' => $topResults,
            'result' => $result,
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
        return $this->render('quiz/leaderboard.html.twig',[
            'quiz'=>$quiz,
            'pagination'=>$pagination
        ]);
    }
}