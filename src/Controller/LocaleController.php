<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LocaleController extends AbstractController
{
    private const COOKIE_LIFETIME = 60 * 60 * 24 * 365;

    /**
     * @Route ("/locale/{_locale}", name="app_locale")
     * @param Request $request
     * @param string $_locale
     * @param UrlGeneratorInterface $urlGenerator
     * @return RedirectResponse
     */
    public function changeLocale(Request $request, string $_locale, UrlGeneratorInterface $urlGenerator): Response
    {
        $request->getSession()->set('_locale', $_locale);
        if ($targetPath = $request->headers->get("referer")) {
            $response = new RedirectResponse($targetPath);
        } else {
            $response = new RedirectResponse($urlGenerator->generate("app_home"));
        }
        $cookie = new Cookie('_locale', $_locale, time() + self::COOKIE_LIFETIME);
        $response->headers->setCookie($cookie);

        return $response;
    }
}