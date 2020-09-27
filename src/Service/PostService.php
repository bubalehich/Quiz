<?php
declare(strict_types=1);

namespace App\Service;


use App\Repository\PostRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class PostService
{
    private const POSTS_PER_PAGE = 10;
    private PostRepository $postRepository;
    private PaginatorInterface $paginator;

    /**
     * PostService constructor.
     * @param PostRepository $postRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $this->postRepository = $postRepository;
        $this->paginator = $paginator;
    }

    public function getPagination(int $page, ?string $find = null):PaginationInterface
    {
        return $this->paginator
            ->paginate($this->postRepository->getPaginationQuery($find), $page, self::POSTS_PER_PAGE);
    }
}