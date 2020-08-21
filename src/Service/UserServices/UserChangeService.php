<?php
declare(strict_types=1);

namespace App\Service\UserServices;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\QuizUser;

class UserChangeService
{
    public function changeActiveField($id,$field,$entityManager){
        $user = $entityManager->getRepository(QuizUser::class)->find($id);
        $user->setIsActive($field);
        $entityManager->persist($user);
        $entityManager->flush();
        return true;
    }
    public function deleteUser($id,$entityManager){
        $user = $entityManager->getRepository(QuizUser::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
    }
}