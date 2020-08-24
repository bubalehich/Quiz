<?php


namespace App\Repository;


use App\Entity\User;

interface UserRepository
{
    function create(User $user): void;

    function update(User $user): void;

    function deleteById(int $id): void;

    function isExistByEmailAndName(User $user): bool;

    function getByEmailAndPassword(string $email, string $password): User;

    function findById(int $id):User;
}