<?php


namespace App\Controller;

use App\Service\UserServices\RegistrateService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\QuizUser;
use App\Form\Type\RegisterType;

class RegisterController extends AbstractController
{
    /**
     * @Route("/reg", name="register")
     * @param Request $request
     * @param RegistrateService $registrateService
     * @return Response
     */
    public function show(Request $request, RegistrateService $registrateService){
        $user = new QuizUser();

        $registerForm = $this->createForm(RegisterType::class,$user);
        $registerForm->handleRequest($request);

        if($registerForm->isSubmitted() && $registerForm->isValid()){
            $user = $registerForm->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $registrationResult = $registrateService->registrateUser($user,$entityManager);
            if($registrationResult) {
                return $this->render('register/succesreg.html.twig');
            }else{
                return $this->render('register/errorreg.html.twig');
            }
        }
        return $this->render('register/reg.html.twig',['regform'=>$registerForm->createView()]);
    }
}