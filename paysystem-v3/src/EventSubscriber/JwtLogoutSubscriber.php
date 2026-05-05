<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * Symfony Security firewall.logout инвалидирует session, но про наш JWT cookie
 * (`access_token`) не знает — стираем его руками на LogoutEvent.
 */
final class JwtLogoutSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $response = new RedirectResponse('/login');
        $response->headers->clearCookie('access_token', '/');
        $event->setResponse($response);
    }
}
