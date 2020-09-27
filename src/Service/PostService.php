<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class PostService
{
    private const POSTS_PER_PAGE = 10;
    private PostRepository $postRepository;
    private PaginatorInterface $paginator;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * PostService constructor.
     * @param PostRepository $postRepository
     * @param PaginatorInterface $paginator
     * @param EntityManagerInterface $em
     */
    public function __construct
    (
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $em
    )
    {
        $this->postRepository = $postRepository;
        $this->paginator = $paginator;
        $this->em = $em;
    }

    /**
     * @param int $page
     * @param string|null $find
     * @return PaginationInterface
     */
    public function getPagination(int $page, ?string $find = null): PaginationInterface
    {
        return $this->paginator
            ->paginate($this->postRepository->getPaginationQuery($find), $page, self::POSTS_PER_PAGE);
    }

    /**
     * @param Post $post
     */
    public function delete(Post $post): void
    {
        $user = $post->getUser();
        $user->removePost($post);
        $this->em->remove($post);
        $this->em->persist($user);
        $this->em->persist($post);
        $this->em->flush();
    }

    /**
     * @param Post $post
     * @param User $user
     */
    public function save(Post $post, User $user): void
    {
        $user->addPost($post);
        $post->setUser($user)->setDate(new DateTime());
        $this->em->persist($user);
        $this->em->persist($post);
        $this->em->flush();
    }

    public function edit(Post $post): void
    {
        $this->em->persist($post->setIsModified(true));
        $this->em->flush();
    }
}