<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Question;
use App\Form\CreateQuestionFormType;
use App\Form\CreateQuizType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AdminService;

/**
 * @Route ("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * @Route ("/", name = "admin_page")
     */
    public function onAdmin(): Response
    {

        return new RedirectResponse($this->generateUrl('app_show_users'));
    }

    /**
     * @Route ("/create_quiz", name = "create_quiz_page")
     */
    public function onAdminQuizCreate(): Response
    {
        $questions = $this->adminService->getQuestions();

        return $this->render('admin/admin_create_quiz.html.twig', ["questions" => $questions]);
    }

    /**
     * @Route ("/create_question", name = "app_create_question_page")
     */
    public function onAdminQuestionCreate(): Response
    {
        $question = new Question();
        $createQuestionForm = $this->createForm(CreateQuestionFormType::class, $question);

        return $this->render('admin/admin_create_question.html.twig', ["createQuestionForm" => $createQuestionForm->createView()]);
    }

    /**
     * @Route ("/users", name = "app_show_users")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function onAdminUserShow(Request $request, PaginatorInterface $paginator): Response
    {
        $users = $this->adminService
            ->getUsersPage($paginator,
                (int)$request->query->get("page", 1));

        return $this->render('admin/admin_show_users.html.twig', ['users' => $users]);
    }

    /**
     * @Route ("/block_user/{id}/{flag}", name = "app_block_user")
     * @param Request $request
     * @return RedirectResponse
     */
    public function onAdminBlockUser(Request $request)
    {
        $this->adminService->blockUser($request->get('id'), $request->get('flag'));

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route ("/edit_user/{id}", name = "app_edit_user")
     * @param Request $request
     * @return Response
     */
    public function onAdminEditUser(Request $request)
    {
        $user = $this->adminService->getUserById($request->get('id'));
        
        return $this->render('admin/admin_edit_user.html.twig', ["user" => $user]);
    }

    /**
     * @Route ("/save_user", name = "app_save_edited_user")
     * @param Request $request
     * @return Response
     */
    public function onAdminUpdateUser(Request $request)
    {

        $this->adminService->updateUser(
            $request->get('id'),
            $request->get('name'),
            $request->get('email'),
            $request->get('verified'));

        return new RedirectResponse($this->generateUrl('app_show_users'));
    }

}