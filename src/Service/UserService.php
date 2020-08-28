<?php
declare(strict_types=1);


namespace App\Service;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $passwordEncoder;
    private $userRepository;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    public function checkForExistance(string $email)
    {
        $user = $this->userRepository->findUserByEmail($email);
        return $user;
    }

    public function registerUserFromForm($registerForm)
    {
        $user = $registerForm->getData();
        $registrationResult = $this->registerUser($user);

        $message = "registration failed!";
        if ($registrationResult) $message = "succesfull registration!";
        return $message;
    }

    public function registerUser(User $user)
    {
        if (!$this->checkForExistance($user->getEmail())) {
            $user->setRoles(['ROLE_USER'])
                ->setIsActive(true)
                ->setIsConfirmed(false)
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()));
            $this->userRepository->registerUser($user);
            return true;
        }
        return false;
    }

    public function executeOperation(int $id, string $operation)
    {
        switch ($operation) {
            case "lock":
                return $this->changeActiveField($id, 0);
            case "unlock":
                return $this->changeActiveField($id, 1);
            case "delete":
                return $this->deleteUser($id);
            default:
        }
    }

    public function changeActiveField(int $id, int $field)
    {
        return $this->userRepository->changeUserActive($id, $field);
    }

    public function deleteUser(int $id)
    {
        $this->userRepository->deleteUser($id);
    }
}