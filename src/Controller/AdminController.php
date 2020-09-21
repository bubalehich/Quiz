<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\CreateQuestionFormType;
use App\Form\CreateQuizType;
use App\Form\QuestionCreateType;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AdminService;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route ("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    private AdminService $adminService;
    private ValidatorInterface $validator;

    public function __construct(AdminService $adminService,ValidatorInterface $validator)
    {
        $this->adminService = $adminService;
        $this->validator = $validator;
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
     * @param Request $request
     * @return Response
     */
    public function onAdminQuestionCreate(Request $request): Response
    {
        $question = new Question();

        $createQuestionForm = $this->createForm(QuestionCreateType::class, $question);
        $createQuestionForm->handleRequest($request);

        if ($createQuestionForm->isSubmitted()){
            dump($question);
            $this->adminService->saveNewQuestion($question);
            return new RedirectResponse($this->generateUrl('app_show_questions'));
        }

        return $this->render('admin/admin_create_question.html.twig', [
            "createQuestionForm" => $createQuestionForm->createView()
        ]);
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
     * @Route ("/questions", name = "app_show_questions")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function onAdminQuestionsShow(Request $request, PaginatorInterface $paginator): Response
    {
        $questions = $this->adminService
            ->getQuestionsPage($paginator,
                (int)$request->query->get("page", 1));

        return $this->render('admin/admin_show_questions.html.twig', ['questions' => $questions]);
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