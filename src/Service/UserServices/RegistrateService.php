<?php


namespace App\Service\UserServices;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class RegistrateService
{
    public function checkForExistance($email,EntityManagerInterface $entityManager){
        $user = $entityManager->getRepository(User::class)->findUserByEmail($email);
        if(!$user){
            return false;
        }else{
            return true;
        }
    }
    public function registrateUser(User $user,EntityManagerInterface $entityManager){
        if(!$this->checkForExistance($user->getEmail(),$entityManager)){
               $user->setIsActive(1);
               $user->setRoleId(0);
               $entityManager->persist($user);
               $entityManager->flush();
               return true;
        }
        return false;
    }
}