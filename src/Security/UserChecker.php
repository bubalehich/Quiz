<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserChecker implements UserCheckerInterface
{
    private TranslatorInterface $translator;

    /**
     * ResetPasswordFormType constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param UserInterface $user
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
        if (!$user->getIsActive()) {
            throw new CustomUserMessageAccountStatusException($this->translator->trans('ex.account.banned'));
        }
        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException($this->translator->trans('ex.account.notconfirmed'));
        }
    }

    /**
     * @param UserInterface $user
     */
    public function checkPostAuth(UserInterface $user)
    {
    }
}