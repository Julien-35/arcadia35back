<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'users')]
    private ?Role $role = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $apiToken = null;

    public function __construct()
    {
        $this->apiToken = bin2hex(random_bytes(20));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }


    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function setApiToken(string $apiToken): static
    {
        $this->apiToken = $apiToken;
        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getRoles(): array
    {
        // On retourne un tableau avec le rôle actuel de l'utilisateur, par exemple "ROLE_USER"
        return $this->role ? ['ROLE_' . strtoupper($this->role->getLabel())] : ['ROLE_USER'];
    }
    

}
