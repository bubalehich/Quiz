<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostFormType;
use App\Service\PostService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class PostController extends AbstractController
{
    private PostService $postService;
    private EntityManagerInterface $em;
    private TranslatorInterface $translator;

    /**
     * PostController constructor.
     * @param PostService $postService
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     */
    public function __construct(PostService $postService, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->postService = $postService;
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @Route ("/posts/", name="app_posts")
     * @param Request $request
     * @return Response
     */
    public function onPostPage(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $searchCriteria = $request->query->get('search');
        $pagination = $this->postService->getPagination($page, $searchCriteria);

        $form = $this->createForm(PostFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**@var Post $post */
            $post = $form->getData();

            /**@var User $user */
            $user = $this->getUser();
            $user->addPost($post);
            $post->setUser($user)->setDate(new DateTime());

            $this->em->persist($user);
            $this->em->persist($post);
            $this->em->flush();

            return $this->redirectToRoute('app_posts');
        }

        return $this->render('post/all.html.twig', ['pagination' => $pagination, 'form' => $form->createView()]);
    }


    /**
     * @Route ("/posts/edit/{id}", name= "app_post_edit")
     * @IsGranted ("ROLE_USER")
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function editPost(Request $request, Post $post): Response
    {
        if (!($this->getUser() === $post->getUser())) {
            return $this->redirectToRoute('app_posts');
        }
        $form = $this->createForm(PostFormType::class, $post, ['msg' => $post->getMessage()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($post->setIsModified(true));
            $this->em->flush();
            $this->addFlash('success', 'Post has been updated!');

            return $this->redirectToRoute('app_posts');
        }

        return $this->render('post/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route ("/posts/delete/{id}", name="app_post_delete")
     * @IsGranted ("ROLE_USER")
     * @param Request $request
     * @param Post $post
     * @return Response
     */
    public function deletePost(Post $post): Response
    {
        if (!($this->getUser() === $post->getUser() || $this->isGranted("ROLE_ADMIN"))) {
            return $this->redirectToRoute('app_posts');
        }
        $user = $post->getUser();
        $user->removePost($post);
        $this->em->remove($post);
        $this->em->persist($user);
        $this->em->persist($post);
        $this->em->flush();

        $this->addFlash('success', 'Post has been deleted!');
        return $this->redirectToRoute('app_posts');
    }
}