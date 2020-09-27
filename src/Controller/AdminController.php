<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\QuizCreateFormType;
use App\Form\QuestionCreateFormType;
use App\Form\UserEditFormType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use App\Repository\UserRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AdminService;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route ("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    private AdminService $adminService;
    private QuestionRepository $questionRepository;
    private UserRepository $userRepository;
    private TranslatorInterface $translator;
    private QuizRepository $quizRepository;

    public function __construct(
        AdminService $adminService,
        QuestionRepository $questionRepository,
        UserRepository $userRepository,
        QuizRepository $quizRepository,
        TranslatorInterface $translator
    )
    {
        $this->userRepository = $userRepository;
        $this->quizRepository = $quizRepository;
        $this->questionRepository = $questionRepository;
        $this->adminService = $adminService;
        $this->translator = $translator;
    }

    /*main method, which redirects to quiz page*/
    /**
     * @Route ("/", name = "admin_page")
     */
    public function onAdmin(): Response
    {
        return new RedirectResponse($this->generateUrl('app_show_quizes'));
    }

    /*method which shows all quizes with filter,sort and edit possibility*/
    /**
     * @Route ("/show_quizes", name = "app_show_quizes")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function onAdminQuizShow(PaginatorInterface $paginator, Request $request): Response
    {
        $name = $request->get('name');
        $quizes = $this->adminService->getQuizzesPage($paginator, (int)$request->query->get("page", 1),$name);

        return $this->render('admin/admin_show_quizes.html.twig', ["quizes" => $quizes]);
    }

    /*method which gives possibility to create new quiz*/
    /**
     * @Route ("/create_quiz", name = "create_quiz_page")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuizCreate(Request $request): Response
    {
        $createQuizForm = $this->createForm(QuizCreateFormType::class);
        $createQuizForm->handleRequest($request);
        if ($createQuizForm->isSubmitted()) {
            if ($this->adminService->saveNewQuiz($createQuizForm->getData())) {
                $this->addFlash('success', 'Quiz created!');

                return new RedirectResponse($this->generateUrl('app_show_quizes'));
            } else {
                $this->addFlash('error', $this->translator->trans('a.error.quiz.unique'));
            }
        }

        return $this->render('admin/admin_create_quiz.html.twig', [
            "createQuizForm" => $createQuizForm->createView(),
        ]);
    }

    /*method which gives possibility tot edit existing quiz*/
    /**
     * @Route ("/edit_quiz/{id}", name = "app_edit_quiz_page")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuizEdit(Request $request): Response
    {
        $quiz = $this->quizRepository->find($request->get('id'));

        $createQuizForm = $this->createForm(QuizCreateFormType::class, $quiz);
        $createQuizForm->handleRequest($request);
        if ($createQuizForm->isSubmitted()) {
            if ($this->adminService->saveNewQuiz($quiz)) {
                $this->addFlash('success', $this->translator->trans('a.quiz.edited'));

                return new RedirectResponse($this->generateUrl('app_show_quizes'));
            } else {
                $this->addFlash('error', $this->translator->trans('a.error.quiz.unique'));
            }
        }

        return $this->render('admin/admin_create_quiz.html.twig', [
            "createQuizForm" => $createQuizForm->createView(),
        ]);
    }

    /*method which gives possibility to delete quiz*/
    /**
     * @Route ("/delete_quiz/{id}", name = "app_delete_quiz")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuizDelete(Request $request): Response
    {
        try {
            if($this->adminService->deleteQuizById($request->get('id'))) {
                $this->addFlash('success', $this->translator->trans('a.flash.quiz.deleted'));
            }else{
                throw new Exception("cannot delete");
            }
        } catch (Exception $ex) {
            $this->addFlash('error', $this->translator->trans('a.error.quiz.notdelete'));
        }

        return new RedirectResponse($this->generateUrl('app_show_quizes'));
    }

    /*method which gives possibility to create new question*/
    /**
     * @Route ("/create_question", name = "app_create_question_page")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuestionCreate(Request $request): Response
    {
        $createQuestionForm = $this->createForm(QuestionCreateFormType::class);
        $createQuestionForm->handleRequest($request);

        if ($createQuestionForm->isSubmitted()) {
            if ($this->adminService->saveQuestion($createQuestionForm->getData())) {
                $this->addFlash('success', $this->translator->trans('a.flash.question.created'));

                return new RedirectResponse($this->generateUrl('app_show_questions'));
            } else {
                $this->addFlash('error', $this->translator->trans('a.error.questions.add'));
            }
        }

        return $this->render('admin/admin_create_question.html.twig', [
            "createQuestionForm" => $createQuestionForm->createView()
        ]);
    }

    /*method which gives possibility to edit qustion*/
    /**
     * @Route ("/edit_question/{id}", name = "app_edit_question_page")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuestionEdit(Request $request): Response
    {
        $question = $this->questionRepository->find($request->get('id'));

        $createQuestionForm = $this->createForm(QuestionCreateFormType::class, $question);
        $createQuestionForm->handleRequest($request);

        if ($createQuestionForm->isSubmitted()) {
            $this->adminService->saveQuestion($question);
            $this->addFlash('success',$this->translator->trans('a.question.edited'));

            return new RedirectResponse($this->generateUrl('app_show_questions'));
        }

        return $this->render('admin/admin_create_question.html.twig', [
            "createQuestionForm" => $createQuestionForm->createView()
        ]);
    }

    /*method which gives possibility to delete question*/
    /**
     * @Route ("/delete_question/{id}", name = "app_delete_question")
     * @param Request $request
     * @return Response
     */
    public function onAdminQuestionDelete(Request $request): Response
    {
        try {
            if($this->adminService->deleteQuestionById($request->get('id'))) {
                $this->addFlash('success', $this->translator->trans('a.flash.question.deleted'));
            }else{
                throw new Exception("Cannot delete");
            }
        } catch (Exception $ex) {
            $this->addFlash('error', $this->translator->trans('a.error.question.notdelete'));
        }
        return new RedirectResponse($this->generateUrl('app_show_questions'));
    }

    /*method which shows all users with sort,filter,edit possibilities*/
    /**
     * @Route ("/users", name = "app_show_users")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function onAdminUserShow(Request $request, PaginatorInterface $paginator): Response
    {
        $searchName = $request->query->get('name');
        $searchEmail = $request->query->get('email');

            $users = $this->adminService
                ->getUsersPage($paginator,
                    (int)$request->query->get("page", 1), $searchName, $searchEmail);

        return $this->render('admin/admin_show_users.html.twig', ['users' => $users]);
    }

    /*method which shows all questions with sort,filter,edit possibilities*/
    /**
     * @Route ("/questions", name = "app_show_questions")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function onAdminQuestionsShow(Request $request, PaginatorInterface $paginator): Response
    {
        $name = $request->get('name');
        $questions = $this->adminService
            ->getQuestionsPage($paginator,
                (int)$request->query->get("page", 1),$name);

        return $this->render('admin/admin_show_questions.html.twig', ['questions' => $questions]);
    }

    /*method which gives possibility to block or unblock user*/

    /**
     * @Route ("/block_user/{id}/{flag}", name = "app_block_user")
     * @param Request $request
     * @return RedirectResponse
     */
    public function onAdminBlockUser(Request $request)
    {
        $this->userRepository->changeUserIsActive($request->get('id'), $request->get('flag'));
        $this->addFlash('success',$this->translator->trans('a.user.blocked'));

        return new RedirectResponse($request->headers->get('referer'));
    }

    /*method which gives possibility to edit user*/
    /**
     * @Route ("/edit_user/{id}", name = "app_edit_user")
     * @param Request $request
     * @return Response
     */
    public function onAdminEditUser(Request $request): Response
    {
        $user = $this->userRepository->find($request->get('id'));
        $editUserForm = $this->createForm(UserEditFormType::class, $user);
        $editUserForm->handleRequest($request);
        if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
            $this->userRepository->updateUserByAdmin($user);
            $this->addFlash('success',$this->translator->trans('a.user.edited'));

            return new RedirectResponse($this->generateUrl('app_show_users'));
        }

        return $this->render('admin/admin_edit_user.html.twig', ["editUserForm" => $editUserForm->createView()]);
    }
}