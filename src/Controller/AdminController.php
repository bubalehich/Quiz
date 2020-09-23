<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\CreateQuestionFormType;
use App\Form\QuizCreateType;
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
     * @Route ("/show_quizes", name = "app_show_quizes")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function onAdminQuizShow(PaginatorInterface $paginator, Request $request): Response
    {
        $quizes = $this->adminService->getQuizesPage($paginator, (int)$request->query->get("page", 1));

        return $this->render('admin/admin_show_quizes.html.twig', ["quizes" => $quizes]);
    }

    /**
     * @Route ("/create_quiz", name = "create_quiz_page")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuizCreate(Request $request): Response
    {
        $quiz = new Quiz();

        $createQuizForm = $this->createForm(QuizCreateType::class, $quiz);
        $createQuizForm->handleRequest($request);
        if ($createQuizForm->isSubmitted()) {
           if($this->adminService->saveNewQuiz($quiz)){
                $this->addFlash('success','Quiz created!');

                return new RedirectResponse($request->headers->get('referer'));
           }else{
               $this->addFlash('error','You have errors, add unique questions!');
           }
        }

        return $this->render('admin/admin_create_quiz.html.twig', [
            "createQuizForm" => $createQuizForm->createView(),
        ]);
    }

    /**
     * @Route ("/edit_quiz/{id}", name = "app_edit_quiz_page")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuizEdit(Request $request): Response
    {
        $quiz = $this->adminService->getQuizById($request->get('id'));

        $createQuizForm = $this->createForm(QuizCreateType::class, $quiz);
        $createQuizForm->handleRequest($request);
        if ($createQuizForm->isSubmitted()) {
            if($this->adminService->saveNewQuiz($quiz)){
                $this->addFlash('success','Quiz saved!');

                return new RedirectResponse($request->headers->get('referer'));
            }else{
                $this->addFlash('error','You have errors, add unique questions!');
            }
        }

        return $this->render('admin/admin_create_quiz.html.twig', [
            "createQuizForm" => $createQuizForm->createView(),
        ]);
    }

    /**
     * @Route ("/delete_quiz/{id}", name = "app_delete_quiz")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuizDelete(Request $request): Response
    {
       $this->adminService->deleteQuizById($request->get('id'));
       return new RedirectResponse($this->generateUrl('app_show_quizes'));
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

        if ($createQuestionForm->isSubmitted()) {
            if ($this->adminService->saveQuestion($question)) {
                $this->addFlash('success', 'Question created!');
                return new RedirectResponse($this->generateUrl('app_show_questions'));
            } else {
                $this->addFlash('error', 'Add some answers!');
                return new RedirectResponse($request->headers->get('referer'));
            }

        }

        return $this->render('admin/admin_create_question.html.twig', [
            "createQuestionForm" => $createQuestionForm->createView()
        ]);
    }

    /**
     * @Route ("/edit_question/{id}", name = "app_edit_question_page")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuestionEdit(Request $request): Response
    {
        $question = $this->adminService->getQuestionById($request->get('id'));

        $createQuestionForm = $this->createForm(QuestionCreateType::class, $question);
        $createQuestionForm->handleRequest($request);

        if ($createQuestionForm->isSubmitted()) {
            $this->adminService->saveQuestion($question);
            return new RedirectResponse($this->generateUrl('app_show_questions'));
        }

        return $this->render('admin/admin_create_question.html.twig', [
            "createQuestionForm" => $createQuestionForm->createView()
        ]);
    }

    /**
     * @Route ("/delete_question/{id}", name = "app_delete_question")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuestionDelete(Request $request): Response
    {
        $this->adminService->deleteQuestionById($request->get('id'));
        return new RedirectResponse($request->headers->get('referer'));
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