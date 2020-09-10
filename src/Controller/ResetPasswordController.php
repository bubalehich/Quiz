<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Security\EmailManager;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @Route("/reset-password")
 */
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private ResetPasswordHelperInterface $resetPasswordHelper;
    private EmailManager $emailManager;
    private UserService $userService;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, EmailManager $emailManager,
                                UserService $userService)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->emailManager = $emailManager;
        $this->userService = $userService;
    }

    /**
     * @Route("", name="app_forgot_password_request")
     * @param Request $request
     * @return Response
     */
    public function sendForgotPasswordRequest(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData()
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/check-email", name="app_check_email")
     */
    public function checkEmail(): Response
    {
        if (!$this->canCheckEmail()) {
            return $this->redirectToRoute('app_forgot_password_request');
        }

        return $this->render('reset_password/check_email.html.twig', [
            'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
        ]);
    }

    /**
     * @Route("/reset/{token}", name="app_reset_password")
     * @param Request $request
     * @param string|null $token
     * @return Response
     */
    public function resetPassword(Request $request, string $token = null): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                'There was a problem validating your reset request - %s',
                $e->getReason()
            ));
          
            return $this->redirectToRoute('app_forgot_password_request');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordHelper->removeResetRequest($token);
            $this->userService->updatePassword($user, $form->get('plainPassword')->getData());
            $this->cleanSessionAfterReset();
            $this->addFlash('success', 'Password has been updated!');
          
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData): RedirectResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $emailFormData]);
        $this->setCanCheckEmailInSession();

        if (!$user) {
            $this->addFlash('reset_password_error', 'There is no user with given email.');

            return $this->redirectToRoute('app_forgot_password_request');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                'There was a problem handling your password reset request - %s',
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        $this->emailManager->sendEmailRequestForgotPassword(
            $user->getEmail(),
            $resetToken,
            $this->resetPasswordHelper->getTokenLifetime()
        );

        return $this->redirectToRoute('app_check_email');
    }
}