<?php
namespace App\EventSubscriber;

use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onException'];
    }

    public function onException(ExceptionEvent $event): void
    {
        if (!str_starts_with($event->getRequest()->getPathInfo(), '/api')) {
            return;
        }

        $e = $event->getThrowable();
        [$status, $message] = match (true) {
            $e instanceof ValidationException => [422, $e->getMessage()],
            $e instanceof NotFoundException   => [404, $e->getMessage()],
            default                            => [500, 'Internal Server Error'],
        };

        $event->setResponse(new JsonResponse(['error' => $message], $status));
    }
}