<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\QuizUser;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function show(){
        /** @var \App\Entity\QuizUser $user */
        $user = $this->getUser();
        return $this->render('index/index.html.twig',["user"=>$user]);
    }
}