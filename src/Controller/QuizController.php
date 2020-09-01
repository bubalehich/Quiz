<?php


namespace App\Controller;


use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class QuizController
 * @Route("/quizes")
 * @IsGranted("ROLE_USER")
 */
class QuizController extends AbstractController
{
    /**
     * @Route("/", name="app_quizes")
     */
    public function showQuizes(): Response
    {
        //TODO add loadinq quizes
//        /** @var Quiz[] $quizes */
//        $quizes = $this->getDoctrine()->getManager()->getRepository(QuizRepository::class)->findAll();
        return $this->render('quiz/all_quizes.html.twig');
    }
}