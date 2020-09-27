<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class TargetPathRequestSubscriber implements EventSubscriberInterface
{
    private SessionInterface $session;
    private UrlGeneratorInterface $generator;
    use TargetPathTrait;

    /**
     * TargetPathRequestSubscriber constructor.
     * @param SessionInterface $session
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(SessionInterface $session, UrlGeneratorInterface $generator)
    {
        $this->session = $session;
        $this->generator = $generator;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (
            !$event->isMasterRequest()
            || $request->isXmlHttpRequest()
            || 'app_login' === $request->attributes->get('_route')
        ) {
            return;
        }

        if (substr_count($request->getUri(), 'reset')) {
            $this->saveTargetPath($this->session, 'main', $this->generator->generate('app_home'));
        } else {
            $this->saveTargetPath($this->session, 'main', $request->getUri());
        }
    }

    /**
     * @return \string[][]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest']];
    }
}