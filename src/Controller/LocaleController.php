<?php


namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    private const COOCKIE_LIFETIME = 3600*3600*2;

    /**
     * @Route ("/locale/{_locale}", name="app_locale")
     * @param Request $request
     * @param string $_locale
     * @return RedirectResponse
     */
    public function changeLocale(Request $request,$_locale)
    {
        $request->getSession()->set('_locale', $_locale);
        $response = new RedirectResponse('/');
        $cookie = new Cookie('_locale',$_locale,time()+self::COOCKIE_LIFETIME);
        $response->headers->setCookie($cookie);

        return $response;
    }
}