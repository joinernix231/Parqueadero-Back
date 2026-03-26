<?php

namespace App\Domain\Entities;

class User
{
    public function __construct(
        private ?int $id = null,
        private string $name = '',
        private string $email = '',
        private string $password = '',
        private string $role = 'operator',
        private ?string $createdAt = null,
        private ?string $updatedAt = null
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isGuard(): bool
    {
        return $this->role === 'guard';
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRole(string $role): void
    {
        if (! in_array($role, ['admin', 'operator', 'guard'])) {
            throw new \InvalidArgumentException("Invalid role: {$role}");
        }
        $this->role = $role;
    }
}
