<?php
declare(strict_types=1);

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use App\Service\AdminServices\AdminService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    private $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * @Route("/admin",name="admin")
     */
    public function defaultShow()
    {
        return $this->redirectToRoute("admin_users");
    }

    /**
     * @Route("/admin/users",name="admin_users")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function showUsers(Request $request, PaginatorInterface $paginator)
    {
        $users = $this->adminService
            ->getUsersPage($paginator,
                (int)$request->query->get("page", 1));

        return $this->render('admin/admin.html.twig', array('users' => $users));

    }

    /**
     * @Route("/admin/quizes",name="admin_quizes")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function showQuizes(Request $request, PaginatorInterface $paginator)
    {
        $quizes = $this->adminService
            ->getQuizesPage($paginator,
                (int)$request->query->get("page", 1));
        return $this->render('admin/admin.html.twig',["quizes"=>$quizes]);
    }

    /**
     * @Route("/admin/create/quiz",name="admin_create_quiz")
     */
    public function show(Request $request)
    {
        return $this->render('admin/admin.html.twig');

    }

}