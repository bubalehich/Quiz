<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route ("/user/{id}", name="profile")
     * @param User $user
     * @return Response
     */
    public function onLogin(User $user)
    {
        return $this->render('profile.html.twig', ['user' => $user]);
    }
}