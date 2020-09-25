<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordTypeForm;
use App\Form\EditUsernameFormType;
use App\Repository\ResultRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
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
    private UserPasswordEncoderInterface $encoder;
    private EntityManagerInterface $em;

    /**
     * UserController constructor.
     * @param UserService $userService
     * @param ResultRepository $resultRepository
     * @param TranslatorInterface $translator
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $em
     */
    public function __construct(
        UserService $userService,
        ResultRepository $resultRepository,
        TranslatorInterface $translator,
        UserPasswordEncoderInterface $encoder,
        EntityManagerInterface $em
    )
    {
        $this->userService = $userService;
        $this->resultRepository = $resultRepository;
        $this->translator = $translator;
        $this->encoder = $encoder;
        $this->em = $em;
    }

    /**
     * @Route ("/", name="app_profile")
     * @return Response
     */
    public function onUserPage()
    {
        /**@var User $user */
        $user = $this->getUser();
        $results = $this->resultRepository->findByUser($user);
        $places = $this->userService->getAllPlacesForUser($user, $results);
        return $this->render('user/profile.html.twig', ['results' => $results, 'places' => $places]);
    }

    /**
     * @Route ("/edit_pass", name="app_profile_edit_pass")
     * @param Request $request
     * @return Response
     */
    public function editPassword(Request $request): Response
    {
        $passwordForm = $this->createForm(ChangePasswordTypeForm::class);
        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            /**@var User $user */
            $user = $this->getUser();
            $password = $this->encoder->encodePassword($user, ($passwordForm->getData())['oldPassword']);
            if ($password === $user->getPassword()) {
                $user->setPassword($password);
                $this->addFlash('success', $this->translator->trans('u.edit.pass.suc'));
                $this->em->persist($user);
                $this->em->flush();

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
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('u.name.suc'));

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/edit_name.html.twig', ['form' => $nameChangeForm->createView()]);
    }
}