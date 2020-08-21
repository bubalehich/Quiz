<?php
declare(strict_types=1);

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use App\Entity\QuizUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin",name="admin")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
public function administrate(Request $request,PaginatorInterface $paginator){
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    $em = $this->getDoctrine()->getManager();
    $dql   = "SELECT i FROM App\Entity\QuizUser i";
    $query = $em->createQuery($dql);
    $entities = $paginator->paginate(
        $query,
        (int)$request->query->get('page', 1)/*page number*/,
        5/*limit per page*/
    );

    return $this->render('admin/admin.html.twig', array('users'=>$entities));

}

}