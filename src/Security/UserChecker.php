<?php

namespace App\Security;

use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof AppUser) {
            return;
        }
        if (!$user->getIsActive()) {
            throw new CustomUserMessageAccountStatusException('Your user account is banned');
        }
        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('Your account is not confirmed! Check your email and confirm it.');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}