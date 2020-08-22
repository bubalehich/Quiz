<?php


namespace App\Controller;

use App\Service\QuizServices\QuizService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\QuizUser;

class IndexController extends AbstractController
{
    private $quizService;
    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * @Route("/", name="index")
     */
    public function showMain(){
        /** @var QuizUser $user */
        $user = $this->getUser();
        return $this->render('index/index.html.twig',["user"=>$user]);
    }
    /**
     * @Route("/play", name="play")
     */
    public function showQuizes(){
        $quizes = $this->quizService->getQuizes();
        return $this->render('index/index.html.twig',["quizes"=>$quizes]);
    }
}