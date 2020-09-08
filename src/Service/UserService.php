<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Security\EmailConfirmationManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var EmailConfirmationManager
     */
    private EmailConfirmationManager $emailConfirmationManager;
    /**
     * @var UserRepository
     */
    private UserRepository $repository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var RoleRepository
     */
    private RoleRepository $roleRepository;

    /**
     * UserService constructor.
     * @param EmailConfirmationManager $emailConfirmationManager
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param RoleRepository $roleRepository
     */
    public function __construct(EmailConfirmationManager $emailConfirmationManager, UserRepository $userRepository,
                                EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder,
                                RoleRepository $roleRepository)
    {
        $this->emailConfirmationManager = $emailConfirmationManager;
        $this->repository = $userRepository;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
        $this->roleRepository = $roleRepository;
    }

    public function register(User $user): array
    {
        if (!$this->repository->findByEmail($user->getEmail())) {
            $user->addRole(
                $this->roleRepository->findByName('ROLE_USER')
            )
                ->setIsActive(true)
                ->setPassword(
                    $this->passwordEncoder->encodePassword($user, $user->getPassword())
                );
            $this->manager->persist($user);
            $this->manager->flush();
            return ['message' => 'Account has been create. Check your email and confirm it.', 'success' => true];
        }
        return ['message' => 'Account with this email already exist.', 'success' => false];
    }
}