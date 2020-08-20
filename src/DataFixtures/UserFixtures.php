<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\QuizUser;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

      public function __construct(UserPasswordEncoderInterface $passwordEncoder)
      {
          $this->passwordEncoder = $passwordEncoder;
      }
    public function load(ObjectManager $manager)
    {

        $user = new QuizUser();
        $user->setName("yana");
        $user->setEmail("yana@mail.ru");
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            '1234'));
        $manager->persist($user);
        $manager->flush();
    }
}
