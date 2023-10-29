<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: '`useroptions`')]
class UserOption extends Option
{
    #[ORM\ManyToOne(inversedBy: 'options')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?User $user = null;

    public function __construct()
    {
        $this->autoload = false;
    }

    public static function of(Option $option): static
    {
        $userOption              = new static();
        $userOption->name        = $option->name;
        $userOption->setValue($option->value);
        $userOption->description = $option->description;
        return $userOption;
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
}
