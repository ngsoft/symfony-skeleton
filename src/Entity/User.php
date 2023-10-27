<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable, \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id          = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles      = [];

    /**
     * The hashed password.
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AccessToken::class)]
    private Collection $tokens;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->username;
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

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

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
