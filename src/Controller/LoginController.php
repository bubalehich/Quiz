<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\Login\LoginType;
use App\Form\Login\RegisterType;
use App\Service\UserServices\RegistrateService;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function list(Request $request, RegistrateService $registrateService)
    {
        $user = new User();

        $registerForm = $this->createForm(RegisterType::class,$user);
        $registerForm->handleRequest($request);

        if($registerForm->isSubmitted() && $registerForm->isValid()){
            $user = $registerForm->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $registrationResult = $registrateService->registrateUser($user,$entityManager);
            if($registrationResult) {
                return $this->render('login/succesreg.html.twig');
            }else{
                return $this->render('login/errorreg.html.twig');
            }
        }

        $loginform = $this->createForm(LoginType::class,$user);
        $loginform->handleRequest($request);

        return $this->render('login/login.html.twig',['regform'=>$registerForm->createView(),'loginform'=>$loginform->createView()]);
    }
}