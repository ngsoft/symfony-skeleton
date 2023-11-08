<?php

declare(strict_types=1);

namespace App\Utils;

use App\Traits\HasAttributes;

class ApiPayload implements \JsonSerializable, \Stringable
{
    use HasAttributes;

    private int $statusCode  = 200;

    private mixed $result    = null;

    private ?ApiError $error = null;

    public function __toString(): string
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
    }

    public function __serialize(): array
    {
        return [$this->statusCode, $this->error, $this->result, $this->attributes];
    }

    public function __unserialize(array $data): void
    {
        [$this->statusCode, $this->error, $this->result, $this->attributes] = $data;
    }

    public static function new(mixed $result = null): static
    {
        $i         = new static();
        $i->result = $result;

        return $i;
    }

    public static function withError(array|int $type, string $description = ''): static
    {
        return (new static())->setError(ApiError::new($type, $description));
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function setResult(mixed $result): static
    {
        $this->result = $result;
        return $this;
    }

    public function getError(): ?ApiError
    {
        return $this->error;
    }

    public function setError(ApiError $error): ApiPayload
    {
        $this->error      = $error;
        $this->statusCode = $error->getCode();
        return $this;
    }

    public function jsonSerialize(): array
    {
        $result           = [];

        if (isset($this->error))
        {
            $result['error'] = $this->error;
            return $result;
        }

        $result           = array_replace($result, $this->getAttributes());
        $result['result'] = $this->result;
        return $result;
    }
}
