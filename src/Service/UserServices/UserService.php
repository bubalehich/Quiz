<?php
declare(strict_types=1);


namespace App\Service\UserServices;

use App\Repository\QuizUserRepository;
use App\Entity\QuizUser;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $passwordEncoder;
    private $quizUserRepository;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, QuizUserRepository $quizUserRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->quizUserRepository = $quizUserRepository;
    }

    public function checkForExistance(string $email)
    {
        $user = $this->quizUserRepository->findUserByEmail($email);
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

    public function registerUser(QuizUser $user)
    {
        if (!$this->checkForExistance($user->getEmail())) {
            $user->setRoles(['ROLE_USER'])
                ->setIsActive(1)
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()));
            $this->quizUserRepository->registerUser($user);
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
        return $this->quizUserRepository->changeUserActive($id, $field);
    }

    public function deleteUser(int $id)
    {
        $this->quizUserRepository->deleteUser($id);
    }
}