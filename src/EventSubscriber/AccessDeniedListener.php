<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Utils\ApiError;
use App\Utils\ApiPayload;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

        $request   = $event->getRequest();

        if ($exception instanceof HttpException && 401 === $exception->getStatusCode())
        {
            $this->setResponse($event, 'Invalid credentials.');
        } elseif (
            str_starts_with($request->getPathInfo(), '/api')
            || '/user/token' === $request->getPathInfo()
            || str_contains($request->headers->get('content-type', ''), '/json')
        ) {
            if (
                $exception instanceof BadCredentialsException
                || $exception instanceof AccessDeniedException
            ) {
                $this->setResponse($event, $exception->getMessage() ?: $exception->getMessageKey());
            } elseif ($exception instanceof NotFoundHttpException && str_contains($exception->getMessage(), 'query parameter'))
            {
                $payload = ApiPayload::withError(ApiError::BAD_REQUEST);

                $event->setResponse(
                    new JsonResponse(
                        $payload,
                        $payload->getStatusCode()
                    )
                );
                $event->stopPropagation();
            } elseif ($exception instanceof MethodNotAllowedHttpException)
            {
                $payload = ApiPayload::withError(ApiError::NOT_ALLOWED);

                $event->setResponse(
                    new JsonResponse(
                        $payload,
                        $payload->getStatusCode()
                    )
                );
                $event->stopPropagation();
            } elseif ($exception instanceof NotFoundHttpException)
            {
                $payload = ApiPayload::withError(ApiError::RESOURCE_NOT_FOUND);

                $event->setResponse(
                    new JsonResponse(
                        $payload,
                        $payload->getStatusCode()
                    )
                );
                $event->stopPropagation();
            }
        }
    }

    protected function setResponse(ExceptionEvent $event, $message, int $code = 0): void
    {
        $code = 0 !== $code ? $code : Response::HTTP_UNAUTHORIZED;

        $event->setResponse(new JsonResponse(
            ApiPayload::withError($code, $message),
            $code
        ));
        $event->stopPropagation();
    }
}
