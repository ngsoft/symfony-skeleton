<?php

declare(strict_types=1);

// src/EventListener/AccessDeniedListener.php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class AccessDeniedListener implements EventSubscriberInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator) {}

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
            $event->setResponse(new JsonResponse([
                'error' => 'Invalid credentials.',
            ], Response::HTTP_UNAUTHORIZED));
            $event->stopPropagation();
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
                $event->setResponse(new JsonResponse([
                    'error' => $exception->getMessage() ?: $exception->getMessageKey(),
                ], Response::HTTP_UNAUTHORIZED));
                $event->stopPropagation();
            }
        }
    }
}
