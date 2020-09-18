<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mime\Address;

class EmailManager
{
    private const EMAIL = 'quiz.sender.bot@gmail.com';
    private const VERIFY_EMAIL_TEMPLATE = 'app_verify_email';
    private const RESTORE_PASSWORD_TEMPLATE = 'reset_password/email.html.twig';
    private const CONFIRMATION_TEMPLATE = 'registration/confirmation_email.html.twig';

    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;
    private UserRepository $repository;

    /**
     * EmailManager constructor.
     * @param VerifyEmailHelperInterface $verifyEmailHelper
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $repository
     * @param TranslatorInterface $translator
     */
    public function __construct
    (
        VerifyEmailHelperInterface $verifyEmailHelper,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager,
        UserRepository $repository,
        TranslatorInterface $translator
    )
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    public function sendEmailConfirmation(User $user): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address(self::EMAIL, $this->translator->trans('bot.author')))
            ->to($user->getEmail())
            ->subject($this->translator->trans('bot.confirm'))
            ->htmlTemplate(self::CONFIRMATION_TEMPLATE);

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            self::VERIFY_EMAIL_TEMPLATE,
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

    public function sendEmailRequestForgotPassword(string $email, ResetPasswordToken $resetToken, int $tokenLifetime): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address(self::EMAIL, $this->translator->trans('bot.author')))
            ->to($email)
            ->subject($this->translator->trans('bot.restore'))
            ->htmlTemplate(self::RESTORE_PASSWORD_TEMPLATE)
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $tokenLifetime,
            ]);
        $this->mailer->send($templatedEmail);
    }
}