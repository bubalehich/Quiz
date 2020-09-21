<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordTypeForm;
use App\Form\EditUsernameFormType;
use App\Repository\ResultRepository;
use App\Service\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route ("/user")
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    private UserService $userService;
    private ResultRepository $resultRepository;
    private TranslatorInterface $translator;

    /**
     * UserController constructor.
     * @param UserService $userService
     * @param ResultRepository $resultRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        UserService $userService,
        ResultRepository $resultRepository,
        TranslatorInterface $translator
    )
    {
        $this->userService = $userService;
        $this->resultRepository = $resultRepository;
        $this->translator = $translator;
    }

    /**
     * @Route ("/", name="app_profile")
     * @return Response
     */
    public function onUserPage()
    {
        $results = $this->resultRepository->findByUser($this->getUser());
        return $this->render('user/profile.html.twig', ['results' => $results]);
    }

    /**
     * @Route ("/edit_pass", name="app_profile_edit_pass")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $passwordForm = $this->createForm(ChangePasswordTypeForm::class);
        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            //service
            $password = $encoder->encodePassword($this->getUser(), ($passwordForm->getData())['oldPassword']);
            /**@var User $user */
            $user = $this->getUser();
            if ($password === $user->getPassword()) {
                $user->setPassword($password);
                $this->addFlash('success', $this->translator->trans('u.edit.pass.suc'));
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('app_profile');
            }
            $this->addFlash('error', $this->translator->trans('u.edit.pass.old.err'));

            return $this->redirectToRoute('app_profile_edit_pass');
        }

        return $this->render('user/edit_pass.html.twig', ['form' => $passwordForm->createView()]);
    }

    /**
     * @Route ("/edit_name",name="app_profile_edit_name")
     * @param Request $request
     * @return Response
     */
    public function editName(Request $request): Response
    {
        $nameChangeForm = $this->createForm(EditUsernameFormType::class);
        $nameChangeForm->handleRequest($request);

        if ($nameChangeForm->isSubmitted() && $nameChangeForm->isValid()) {
            /**@var User $user */
            $user = $this->getUser();
            $user->setName(($nameChangeForm->getData())['name']);
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', $this->translator->trans('u.name.suc'));

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/edit_name.html.twig', ['form' => $nameChangeForm->createView()]);
    }
}