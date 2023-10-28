<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username', 'email'], message: 'There is already an account with this username/email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface, \Stringable, \JsonSerializable
{
    public const ROLE_USER        = 'ROLE_USER';
    public const ROLE_ADMIN       = 'ROLE_ADMIN';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @var array<string,bool> role, writable
     */
    public const BUILTIN_ROLES    = [
        self::ROLE_USER        => false,
        self::ROLE_ADMIN       => true,
        self::ROLE_SUPER_ADMIN => true,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id              = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username     = null;

    #[ORM\Column]
    private array $roles          = [];

    /**
     * The hashed password.
     */
    #[ORM\Column]
    private ?string $password     = null;

    #[ORM\Column('fullname', length: 255, nullable: true)]
    private ?string $fullName     = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $email        = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AccessToken::class)]
    private Collection $tokens;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => true])]
    private bool $enabled         = true;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles   = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function addRole(string $role): static
    {
        return $this->setRoles(
            array_merge(
                $this->roles,
                [$role]
            )
        );
    }

    public function removeRole(string $role): static
    {
        $this->roles = array_filter($this->roles, fn ($r) => $r !== $role);
        return $this;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = array_filter(
            array_unique($roles),
            fn ($role) => $this->canAddRole($role)
        );

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, AccessToken>
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(AccessToken $token): static
    {
        if ( ! $this->tokens->contains($token))
        {
            $this->tokens->add($token);
            $token->setUser($this);
        }

        return $this;
    }

    public function removeToken(AccessToken $token): static
    {
        if ($this->tokens->removeElement($token))
        {
            // set the owning side to null (unless already changed)
            if ($token->getUser() === $this)
            {
                $token->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Get a valid token.
     */
    public function getToken(): ?AccessToken
    {
        /** @var AccessToken $token */
        foreach ($this->tokens as $token)
        {
            if ( ! $token->isExpired())
            {
                return $token;
            }
        }
        return null;
    }

    public function getApiKey(): ?AccessToken
    {
        /** @var AccessToken $token */
        foreach ($this->tokens as $token)
        {
            if ($token->isPermanent())
            {
                return $token;
            }
        }
        return null;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if ( ! $user instanceof User)
        {
            return false;
        }

        if ($this->password !== $user->getPassword())
        {
            return false;
        }

        if ($this->username !== $user->getUsername())
        {
            return false;
        }

        return true;
    }

    public function getFullName(): string
    {
        return $this->fullName ?? $this->username;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email ?? '';
    }

    public function setEmail(?string $email): static
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $this->email = $email;
        }

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    protected function canAddRole(string $role): bool
    {
        return (static::BUILTIN_ROLES[$role] ?? null) !== false;
    }
}
