<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mime\Address;

class EmailManager
{
    private const EMAIL = 'quiz.sender.bot@gmail.com';
    private const MSG_CONFIRM_EMAIL = 'Hello! You created an account on Quiz. Please confirm your email.';
    private const MSG_RESTORE_PASSWORD = 'Hello! You send request for restoring password. Here is your link.';
    private const SENDER_NAME = 'Quiz Bot';
    private const VERIFY_EMAIL_ROUTE_NAME = 'app_verify_email';
    private const CONFIRMATION_TEMPLATE = 'registration/confirmation_email.html.twig';

    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;

    /**
     * EmailManager constructor.
     * @param VerifyEmailHelperInterface $verifyEmailHelper
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(VerifyEmailHelperInterface $verifyEmailHelper, MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    public function sendEmailConfirmation(User $user): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address(self::EMAIL, self::SENDER_NAME))
            ->to($user->getEmail())
            ->subject(self::MSG_CONFIRM_EMAIL)
            ->htmlTemplate(self::CONFIRMATION_TEMPLATE);

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            self::VERIFY_EMAIL_ROUTE_NAME,
            (string)$user->getId(),
            $user->getEmail()
        );

        $context = $templatedEmail->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl() . '&id=' . $user->getId();
        $context['expiresAt'] = $signatureComponents->getExpiresAt();
        $templatedEmail->context($context);
        $this->mailer->send($templatedEmail);
    }

    /**
     * @param Request $request
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request): void
    {
        $user = $this->entityManager->getRepository(User::class)
            ->find($request->get('id'));
        $uri = str_replace
        (
            '&id=' . (string)$user->getId(),
            '',
            $request->getUri()
        );
        $this->verifyEmailHelper->validateEmailConfirmation($uri, (string)$user->getId(),
            $user->getEmail());
        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}