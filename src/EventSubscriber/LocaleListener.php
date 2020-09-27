<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    private string $defaultLocale;

    /**
     * LocaleListener constructor.
     * @param string $defaultLocale
     */
    public function __construct(string $defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else if (($locale1 = $request->cookies->get('_locale'))) {
            $request->setLocale($locale1);
        } else {
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    /**
     * @return \array[][]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => array(array('onKernelRequest', 15))];
    }
}