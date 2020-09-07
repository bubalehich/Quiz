<?php
declare(strict_types=1);

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route ("/user", name="app_profile")
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function onUserPage()
    {
        return $this->render('user/profile.html.twig');
    }
}