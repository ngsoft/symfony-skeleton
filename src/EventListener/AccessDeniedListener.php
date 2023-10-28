<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class AccessDeniedListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            // the priority must be greater than the Security HTTP
            // ExceptionListener, to make sure it's called before
            // the default exception listener
            KernelEvents::EXCEPTION => ['onKernelException', 2],
            HttpException::class    => ['onKernelException', 2],

        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpException && 401 === $exception->getStatusCode())
        {
            $this->setResponse($event, 'Invalid credentials.');
        } elseif (
            $exception instanceof BadCredentialsException
            || $exception instanceof AccessDeniedException
        ) {
            $request = $event->getRequest();

            if (
                str_starts_with($request->getPathInfo(), '/api')
                || '/user/token' === $request->getPathInfo()
                || str_contains($request->headers->get('content-type', ''), '/json')
            ) {
                $this->setResponse($event, $exception->getMessage() ?: $exception->getMessageKey());
            }
        }
    }

    protected function setResponse(ExceptionEvent $event, $message): void
    {
        $event->setResponse(new JsonResponse([
            'error' => $message,
        ], Response::HTTP_UNAUTHORIZED));
        $event->stopPropagation();
    }
}
