<?php


namespace App\Controller;


use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuizController
 * @Route("/quizes")
 * @IsGranted("ROLE_USER")
 */
class QuizController extends AbstractController
{
    private QuizRepository $quizRepository;

    /**
     * QuizController constructor.
     * @param QuizRepository $quizRepository
     */
    public function __construct(QuizRepository $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    /**
     * @Route("/", name="app_quizes")
     */
    public function listAction(Request $request): Response
    {
        $pagination = $this->quizRepository->findNext($request->query->getInt('page', 1));
        return $this->render('quiz/all_quizes.html.twig', ['pagination' => $pagination]);
    }
}