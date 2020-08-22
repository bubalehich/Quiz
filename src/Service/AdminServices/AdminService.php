<?php
declare(strict_types=1);

namespace App\Service\AdminServices;


use App\Repository\QuizUserRepository;
use Knp\Component\Pager\PaginatorInterface;

class AdminService
{
    private $quizUserRepository;

    public function __construct(QuizUserRepository $quizUserRepository)
    {
        $this->quizUserRepository = $quizUserRepository;
    }

    public function getUsersPage(PaginatorInterface $paginator, int $page)
    {
        return $paginator->paginate(
            $this->quizUserRepository->getPaginatorQuery(),
            $page,
            5
        );
    }
}