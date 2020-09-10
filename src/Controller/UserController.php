<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\ResultRepository;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserService $userService;
    private ResultRepository $resultRepository;

    /**
     * UserController constructor.
     * @param UserService $userService
     * @param ResultRepository $resultRepository
     */
    public function __construct(UserService $userService, ResultRepository $resultRepository)
    {
        $this->userService = $userService;
        $this->resultRepository = $resultRepository;
    }

    /**
     * @Route ("/user", name="app_profile")
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function onUserPage()
    {
        $results = $this->resultRepository->findByUser($this->getUser());
//        dd($results);
        return $this->render('user/profile.html.twig',['results'=>$results]);
    }
}