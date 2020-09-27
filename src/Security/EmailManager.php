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
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;
    private UserRepository $repository;
    private string $email;

    /**
     * EmailManager constructor.
     * @param VerifyEmailHelperInterface $verifyEmailHelper
     * @param MailerInterface $mailer
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $repository
     * @param TranslatorInterface $translator
     * @param string $email
     */
    public function __construct
    (
        VerifyEmailHelperInterface $verifyEmailHelper,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager,
        UserRepository $repository,
        TranslatorInterface $translator,
        string $email
    )
    {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->translator = $translator;
        $this->email = $email;
    }

    public function sendEmailConfirmation(User $user): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address($this->email, $this->translator->trans('bot.author')))
            ->to($user->getEmail())
            ->subject($this->translator->trans('bot.confirm'))
            ->htmlTemplate('registration/confirmation_email.html.twig');

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
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
        $user = $this
            ->entityManager
            ->getRepository(User::class)
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
            ->from(new Address($this->email, $this->translator->trans('bot.author')))
            ->to($email)
            ->subject($this->translator->trans('bot.restore'))
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $tokenLifetime,
            ]);
        $this->mailer->send($templatedEmail);
    }
}