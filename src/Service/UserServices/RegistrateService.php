<?php


namespace App\Service\UserServices;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\QuizUser;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrateService
{
    private $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function checkForExistance($email,EntityManagerInterface $entityManager){
        $user = $entityManager->getRepository(QuizUser::class)->findUserByEmail($email);
        if(!$user){
            return false;
        }else{
            return true;
        }
    }
    public function registrateUser(QuizUser $user,EntityManagerInterface $entityManager){
        if(!$this->checkForExistance($user->getEmail(),$entityManager)){
              $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $user->getPassword()));
               $entityManager->persist($user);
               $entityManager->flush();
               return true;
        }
        return false;
    }
}