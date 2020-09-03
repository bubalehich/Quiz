<?php

namespace App\Controller;

use App\Service\QuizService;
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
     * @Route("/", name="app_quizes")
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->service->getPagination($page);
        $leaders = $this->service->getLeadersForPage($page);
        return $this->render
        (
            'quiz/all_quizes.html.twig',
            ['pagination' => $pagination, 'leaders' => $leaders]
        );
    }
}