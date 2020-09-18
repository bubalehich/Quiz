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
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailManager $emailVerifier;
    private TranslatorInterface $translator;

    public function __construct(EmailManager $emailVerifier, TranslatorInterface $translator)
    {
        $this->emailVerifier = $emailVerifier;
        $this->translator = $translator;
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
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->register($user = $form->getData());
            $this->emailVerifier->sendEmailConfirmation($user);
            $this->addFlash('success', $this->translator->trans('f.account.create'));

            return $this->redirectToRoute('app_login');
        }
        if ($form->isSubmitted()) {
            $this->addFlash('verify_email_error', $this->translator->trans('f.account.exists'));
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
            $this->addFlash('success', $this->translator->trans('f.account.verify'));
            return $this->redirectToRoute('app_login');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());
            return $this->redirectToRoute('app_register');
        }
    }
}