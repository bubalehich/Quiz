<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private UserRepository $repository;
    private EntityManagerInterface $manager;
    private UserPasswordEncoderInterface $passwordEncoder;
    private RoleRepository $roleRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param RoleRepository $roleRepository
     */
    public function __construct
    (
        UserRepository $userRepository,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        RoleRepository $roleRepository
    )
    {
        $this->repository = $userRepository;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
        $this->roleRepository = $roleRepository;
    }

    public function register(User $user): void
    {
        $user->addRole($this->roleRepository->findByName('ROLE_USER'))
            ->setIsActive(true)
            ->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function updatePassword(User $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $plainPassword
        ));
        $this->manager->flush();
    }
}