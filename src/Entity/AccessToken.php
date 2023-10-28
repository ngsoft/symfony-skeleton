<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AccessTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
class AccessToken implements \Stringable, \JsonSerializable
{
    public const DEFAULT_EXPIRE = '+1 hour';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id            = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $token;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    #[ORM\ManyToOne(inversedBy: 'tokens')]
    private ?User $user;

    #[ORM\Column(type: 'boolean')]
    private bool $permanent     = false;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $name = null;

    public function __construct(?User $user = null)
    {
        $this->token = bin2hex(random_bytes(60));
        $this->renewExpiresAt();

        $this->user  = $user;
    }

    public function __toString(): string
    {
        return $this->token;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return (new \DateTimeImmutable())->getTimestamp() > $this->expiresAt->getTimestamp();
    }

    public function renewExpiresAt(): static
    {
        $this->expiresAt = date_create_immutable(static::DEFAULT_EXPIRE);

        return $this;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isPermanent(): ?bool
    {
        return $this->permanent;
    }

    public function setPermanent(bool $isPermanent): static
    {
        $this->permanent = $isPermanent;

        return $this;
    }

    public function isValid(): bool
    {
        return ($this->permanent || ! $this->isExpired()) && $this->user instanceof User;
    }

    public function jsonSerialize(): array
    {
        return [
            'user'    => $this->user,
            'token'   => $this->token,
            'expires' => $this->expiresAt,
        ];
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
