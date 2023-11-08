<?php

declare(strict_types=1);

namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;

class ApiError extends \RuntimeException implements \JsonSerializable
{
    public const BAD_REQUEST             = ['Bad Request', Response::HTTP_BAD_REQUEST];
    public const INSUFFICIENT_PRIVILEGES = ['Insufficient Privileges', Response::HTTP_UNAUTHORIZED];
    public const NOT_ALLOWED             = ['Not Allowed', Response::HTTP_METHOD_NOT_ALLOWED];
    public const NOT_IMPLEMENTED         = ['Not Implemented', Response::HTTP_NOT_IMPLEMENTED];
    public const RESOURCE_NOT_FOUND      = ['Resource not Found', Response::HTTP_NOT_FOUND];
    public const SERVER_ERROR            = ['Server Error', Response::HTTP_INTERNAL_SERVER_ERROR];
    public const UNAUTHENTICATED         = ['Unauthenticated', Response::HTTP_UNAUTHORIZED];
    public const VALIDATION_ERROR        = ['Validation Error', Response::HTTP_BAD_REQUEST];
    public const VERIFICATION_ERROR      = ['Verification Error', Response::HTTP_BAD_REQUEST];

    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code);
    }

    public static function new(array|int $type, string $description = ''): static
    {
        if (is_array($type))
        {
            foreach ($type as $value)
            {
                if (is_string($value))
                {
                    $description = $value;
                } elseif (is_int($value))
                {
                    $type = $value;
                    break;
                }
            }
        }

        if ( ! is_int($type))
        {
            $type = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new static($description, $type);
    }

    public function jsonSerialize(): string
    {
        return $this->getMessage();
    }
}
