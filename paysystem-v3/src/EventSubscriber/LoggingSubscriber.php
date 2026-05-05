<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class LoggingSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onRequest', 100]];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) return;
        $this->logger->info('Incoming request', [
            'method' => $event->getRequest()->getMethod(),
            'path'   => $event->getRequest()->getPathInfo(),
        ]);
    }
}