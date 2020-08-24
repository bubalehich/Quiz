<?php


namespace App\Repository;


use App\Entity\User;

class UserRepositoryImpl implements UserRepository
{

    function create(User $user): void
    {
        // TODO: Implement create() method.
    }

    function update(User $user): void
    {
        // TODO: Implement update() method.
    }

    function isExistByEmailAndName(User $user): bool
    {
        // TODO: Implement isExistByEmailAndName() method.
    }

    function getByEmailAndPassword(string $email, string $password): User
    {
        // TODO: Implement getByEmailAndPassword() method.
    }

    function findById(int $id): User
    {
        // TODO: Implement findById() method.
    }

    function deleteById(int $id): void
    {
        // TODO: Implement deleteById() method.
    }
}