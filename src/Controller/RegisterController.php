<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\UserServices\RegistrateService;
use App\Service\UserServices\UserChangeService;
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

    /**
     * @Route("/changeuser",name="change_user")
     * @param Request $request
     * @param UserChangeService $userChangeService
     * @return Response
     */
    public function change(Request $request, UserChangeService $userChangeService){
        $operation = $request->get("operation_name");
        $entityManager = $this->getDoctrine()->getManager();
        $id = $request->get("id");
        if($operation=="lock"){
            $userChangeService->changeActiveField($id,0,$entityManager);
        }else if($operation=="unlock"){
            $userChangeService->changeActiveField($id,1,$entityManager);
        }else if($operation=="delete"){
            $userChangeService->deleteUser($id,$entityManager);
        }
        return new Response();
    }
}