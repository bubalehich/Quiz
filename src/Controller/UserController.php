<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\UserServices\UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\QuizUser;
use App\Form\Type\RegisterType;

class UserController extends AbstractController
{
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/reg", name="register")
     * @param Request $request
     * @return Response
     */
    public function show(Request $request)
    {
        $user = new QuizUser();
        $registerForm = $this->createForm(RegisterType::class, $user);
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $message = $this->userService->registerUserFromForm($registerForm);
            return $this->render('register/message.html.twig', ["message" => $message]);

        }
        return $this->render('register/reg.html.twig', ['regform' => $registerForm->createView()]);
    }

    /**
     * @Route("/changeuser",name="change_user")
     * @param Request $request
     * @return Response
     */
    public function change(Request $request)
    {
        $message = $this->userService->executeOperation($request->get("id"),$request->get("operation_name"));
        return new Response($message . "");
    }
}