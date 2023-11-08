<?php

declare(strict_types=1);

namespace App\Security;

use App\Utils\ApiError;
use App\Utils\ApiPayload;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AccessTokenHandlerFailure implements AuthenticationFailureHandlerInterface
{
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new JsonResponse(ApiPayload::withError(ApiError::UNAUTHENTICATED), 401);
    }
}
