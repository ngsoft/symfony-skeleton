<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Utils\ApiError;
use App\Utils\ApiPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiAbstractController extends AbstractController
{
    protected function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        if ($data instanceof ApiPayload && 200 === $status)
        {
            $status = $data->getStatusCode();
        }
        return parent::json($data, $status, $headers, $context);
    }

    protected function apiResponse(mixed $value, array $attributes = []): JsonResponse
    {
        if ($value instanceof ApiError)
        {
            $value = ApiPayload::new()->setError($value);
        } elseif ( ! $value instanceof ApiPayload)
        {
            $value = ApiPayload::new($value);
        }

        return $this->json($value->addAttributes($attributes));
    }
}
