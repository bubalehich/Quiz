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
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function administrate(Request $request, PaginatorInterface $paginator)
    {
        $users = $this->adminService
            ->getUsersPage($paginator,
                (int)$request->query->get("page", 1));

        return $this->render('admin/admin.html.twig', array('users' => $users));

    }

}