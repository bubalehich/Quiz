<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\Security\EmailManager;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailManager $emailVerifier;

    public function __construct(EmailManager $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     * @param UserService $service
     * @param Request $request
     * @return Response
     */
    public function register(UserService $service, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $service->register(
                $user = $form->getData()
            );

            if ($result['success']) {
                $this->emailVerifier->sendEmailConfirmation($user);
                $this->addFlash('success', $result['message']);

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     * @param Request $request
     * @return Response
     */
    public function verifyUserEmail(Request $request): Response
    {
        try {
            $this->emailVerifier->handleEmailConfirmation($request);
            $this->addFlash('success', 'Your email address has been verified.');
            return $this->redirectToRoute('app_login');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('app_register');
        }
    }
}