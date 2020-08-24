<?php


namespace App\Repository\Quiz;


use App\Entity\Quiz;

interface QuizRepository
{
    function create(Quiz $quiz): void;

    function update(Quiz $quiz): void;

    function deleteById(int $id): void;

    function getById(): Quiz;
}