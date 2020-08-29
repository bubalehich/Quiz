<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route ("/user/{id}", name="profile")
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function onLogin(int $id)
    {
        /**@var User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->createAccessDeniedException();
        }
        if ($user->getId() === $id) {
            return $this->render('user/profile.html.twig', ['user' => $user]);
        }
        return $this->redirectToRoute('profile', ['id' => $user->getId()]);
    }
}