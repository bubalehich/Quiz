<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{

    /**
     * @Route("/",name="home")
     */
    public function index(Request $request)
    {
        $user = $this->getUser();
        return $this->render('home.html.twig', ['user' => $user]);
    }
}