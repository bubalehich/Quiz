<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Service\PostService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route ("/posts")
 */
class PostController extends AbstractController
{
    private PostService $postService;
    private TranslatorInterface $translator;

    /**
     * PostController constructor.
     * @param PostService $postService
     * @param TranslatorInterface $translator
     */
    public function __construct(PostService $postService, TranslatorInterface $translator)
    {
        $this->postService = $postService;
        $this->translator = $translator;
    }

    /**
     * @Route ("/", name="app_posts")
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
            $this->postService->save($form->getData(), $this->getUser());

            return $this->redirectToRoute('app_posts');
        }

        return $this->render('post/all_posts.html.twig', ['pagination' => $pagination, 'form' => $form->createView()]);
    }

    /**
     * @Route ("/edit/{id}", name= "app_post_edit")
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
            $this->postService->edit($post);
            $this->addFlash('success', $this->translator->trans('post.msg.upd'));

            return $this->redirectToRoute('app_posts');
        }

        return $this->render('post/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route ("/delete/{id}", name="app_post_delete")
     * @IsGranted ("ROLE_USER")
     * @param Post $post
     * @return Response
     */
    public function deletePost(Post $post): Response
    {
        if (!($this->getUser() === $post->getUser() || $this->isGranted("ROLE_ADMIN"))) {
            return $this->redirectToRoute('app_posts');
        }
        $this->postService->delete($post);

        $this->addFlash('success', $this->translator->trans('post.msg.del'));
        return $this->redirectToRoute('app_posts');
    }
}